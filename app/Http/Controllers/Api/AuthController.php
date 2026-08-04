<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\SendOtpResetPasswordMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'device_name' => ['nullable', 'string'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau kata sandi tidak sesuai.'],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => ['Akun Anda telah dinonaktifkan. Hubungi Admin.'],
            ]);
        }

        $token = $user->createToken($data['device_name'] ?? 'mobile-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $this->formatUser($user),
        ]);
    }

    /**
     * Request Kode OTP untuk Reset Password (Gmail)
     */
    public function requestOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower(trim($request->email));

        $user = User::where('email', $email)->first();

        if (! $user) {
            return response()->json([
                'message' => 'Alamat email tidak terdaftar dalam sistem.',
            ], 404);
        }

        if (! $user->is_active) {
            return response()->json([
                'message' => 'Akun Anda dinonaktifkan. Silakan hubungi Admin HR.',
            ], 403);
        }

        // Cek cooldown 60 detik pengiriman OTP ulang
        $recentOtp = DB::table('password_reset_otps')
            ->where('email', $email)
            ->where('is_used', false)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($recentOtp && Carbon::parse($recentOtp->resend_available_at)->isFuture()) {
            $secondsRemaining = Carbon::parse($recentOtp->resend_available_at)->diffInSeconds(now());
            return response()->json([
                'message' => "Harap tunggu {$secondsRemaining} detik sebelum meminta kode OTP baru.",
                'cooldown_seconds' => $secondsRemaining,
            ], 429);
        }

        // Invalidate OTP lama yang belum diverifikasi
        DB::table('password_reset_otps')
            ->where('email', $email)
            ->where('is_verified', false)
            ->update(['is_used' => true]);

        // Generate OTP 6 digit
        $otp = sprintf('%06d', mt_rand(0, 999999));
        $now = now();

        DB::table('password_reset_otps')->insert([
            'email' => $email,
            'otp' => $otp,
            'expires_at' => $now->copy()->addMinutes(15),
            'resend_available_at' => $now->copy()->addSeconds(60),
            'is_verified' => false,
            'is_used' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Kirim Email via Mailable Gmail
        try {
            Mail::to($email)->send(new SendOtpResetPasswordMail($otp, $user->name));
        } catch (\Exception $e) {
            // Log error namun tetap informasikan kegagalan kirim jika SMTP bermasalah
            \Log::error("Gagal mengirim email OTP reset password ke {$email}: " . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengirimkan kode OTP ke Gmail Anda. Pastikan layanan server mail aktif.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Kode OTP berhasil dikirimkan ke Gmail Anda. Berlaku selama 15 menit.',
            'resend_cooldown' => 60,
        ]);
    }

    /**
     * Verifikasi Kode OTP yang dimasukkan User
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $email = strtolower(trim($request->email));
        $otp = trim($request->otp);

        $otpRecord = DB::table('password_reset_otps')
            ->where('email', $email)
            ->where('otp', $otp)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->first();

        if (! $otpRecord) {
            return response()->json([
                'message' => 'Kode OTP salah atau telah kadaluarsa (maksimal 15 menit).',
            ], 400);
        }

        // Generate temporary Reset Token (64 karakter) untuk sesi reset password
        $resetToken = Str::random(64);

        DB::table('password_reset_otps')
            ->where('id', $otpRecord->id)
            ->update([
                'is_verified' => true,
                'reset_token' => $resetToken,
                'updated_at' => now(),
            ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Kode OTP valid. Silakan masukkan kata sandi baru Anda.',
            'reset_token' => $resetToken,
        ]);
    }

    /**
     * Set / Ubah Password Baru dengan Reset Token
     */
    public function resetPasswordWithOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'reset_token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $email = strtolower(trim($request->email));
        $resetToken = $request->reset_token;

        $otpRecord = DB::table('password_reset_otps')
            ->where('email', $email)
            ->where('reset_token', $resetToken)
            ->where('is_verified', true)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (! $otpRecord) {
            return response()->json([
                'message' => 'Sesi reset password tidak valid atau telah kadaluarsa. Silakan minta OTP baru.',
            ], 400);
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            return response()->json([
                'message' => 'Pengguna tidak ditemukan.',
            ], 404);
        }

        // Update Password
        $user->password = Hash::make($request->password);
        $user->save();

        // Tandai OTP record sudah digunakan
        DB::table('password_reset_otps')
            ->where('id', $otpRecord->id)
            ->update([
                'is_used' => true,
                'updated_at' => now(),
            ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Kata sandi berhasil diperbarui. Silakan masuk menggunakan kata sandi baru.',
        ]);
    }

    /**
     * Legacy Lupa Password (alias ke requestOtp)
     */
    public function lupaPassword(Request $request)
    {
        return $this->requestOtp($request);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Berhasil keluar.']);
    }

    public function me(Request $request)
    {
        return response()->json(['user' => $this->formatUser($request->user())]);
    }

    private function formatUser($user): array
    {
        $user->loadMissing('pegawai.jabatan', 'pegawai.unit');

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'pegawai' => $user->pegawai ? [
                'id' => $user->pegawai->id,
                'nip' => $user->pegawai->nip,
                'nama' => $user->pegawai->nama,
                'jabatan' => $user->pegawai->jabatan?->nama_jabatan,
                'unit' => $user->pegawai->unit?->nama_unit,
                'status_kepegawaian' => $user->pegawai->status_kepegawaian,
                'foto' => (!empty($user->pegawai->foto)) ? asset('storage/' . $user->pegawai->foto) : null,
                'no_hp' => $user->pegawai->no_hp,
                'alamat' => $user->pegawai->alamat,
                'email' => $user->pegawai->email,
            ] : null,
        ];
    }
}
