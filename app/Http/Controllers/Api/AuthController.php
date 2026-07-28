<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
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

        $user = \App\Models\User::where('email', $data['email'])->first();

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

    public function lupaPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        try {
            Password::sendResetLink($request->only('email'));
        } catch (\Exception $e) {
            // Abaikan error pengiriman email agar selalu mengembalikan respon sukses generik demi keamanan.
        }

        return response()->json([
            'message' => 'Jika email terdaftar, link reset password telah dikirim. Silakan cek email Anda atau hubungi Admin HR jika mengalami kendala.'
        ]);
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
