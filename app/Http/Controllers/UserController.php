<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('pegawai');

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->get('role')) {
            $query->where('role', $role);
        }

        if ($request->get('is_active') !== null) {
            $query->where('is_active', (bool) $request->get('is_active'));
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();

        $totalUsers    = User::count();
        $aktifCount    = User::where('is_active', true)->count();
        $nonaktifCount = User::where('is_active', false)->count();
        $adminCount    = User::where('role', 'admin')->count();
        $atasanCount   = User::where('role', 'atasan')->count();
        $pegawaiCount  = User::where('role', 'pegawai')->count();
        $tanpaAkunCount = Pegawai::whereNull('user_id')->count();

        return view('user.index', array_merge(compact(
            'users', 'totalUsers', 'aktifCount', 'nonaktifCount',
            'adminCount', 'atasanCount', 'pegawaiCount', 'tanpaAkunCount'
        ), ['filters' => $request->only(['q', 'role', 'is_active'])]));
    }

    public function create(Request $request)
    {
        $pegawaiTanpaAkun = Pegawai::whereNull('user_id')->orderBy('nama')->get();
        $preselectedId    = $request->get('pegawai_id');

        return view('user.form', [
            'user'             => new User(),
            'pegawaiTanpaAkun' => $pegawaiTanpaAkun,
            'preselectedId'    => $preselectedId,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:8'],
            'role'       => ['required', 'in:admin,atasan,pegawai'],
            'is_active'  => ['required', 'boolean'],
            'pegawai_id' => ['nullable', 'exists:pegawais,id'],
        ]);

        $pegawaiId = $data['pegawai_id'] ?? null;
        unset($data['pegawai_id']);

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        if ($pegawaiId) {
            Pegawai::where('id', $pegawaiId)->update(['user_id' => $user->id]);
        }

        AuditLog::catat('membuat akun user baru', 'User', $user->id, $user->name . ' (' . $user->email . ')');

        return redirect()->route('user.index')
            ->with('status', "Akun user \"{$user->name}\" berhasil dibuat.");
    }

    public function edit(User $user)
    {
        $currentPegawai   = $user->pegawai;
        $pegawaiTanpaAkun = Pegawai::whereNull('user_id')->orderBy('nama')->get();

        if ($currentPegawai) {
            $pegawaiTanpaAkun = $pegawaiTanpaAkun->prepend($currentPegawai);
        }

        return view('user.form', [
            'user'             => $user,
            'pegawaiTanpaAkun' => $pegawaiTanpaAkun,
            'preselectedId'    => $currentPegawai?->id,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password'   => ['nullable', 'string', 'min:8'],
            'role'       => ['required', 'in:admin,atasan,pegawai'],
            'is_active'  => ['required', 'boolean'],
            'pegawai_id' => ['nullable', 'exists:pegawais,id'],
        ]);

        $pegawaiId = $data['pegawai_id'] ?? null;
        unset($data['pegawai_id']);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        $oldPegawai = $user->fresh()->pegawai;
        if ($oldPegawai && $oldPegawai->id != $pegawaiId) {
            $oldPegawai->update(['user_id' => null]);
        }

        if ($pegawaiId) {
            Pegawai::where('id', $pegawaiId)->update(['user_id' => $user->id]);
        }

        AuditLog::catat('memperbarui akun user', 'User', $user->id, $user->name . ' (' . $user->email . ')');

        return redirect()->route('user.index')
            ->with('status', "Akun user \"{$user->name}\" berhasil diperbarui.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        AuditLog::catat('mereset password user', 'User', $user->id, $user->name . ' (' . $user->email . ')');

        return back()->with('status', "Password akun \"{$user->name}\" berhasil direset.");
    }

    public function toggleActive(Request $request, User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri yang sedang digunakan.');
        }

        if ($user->role === 'admin' && $user->is_active) {
            $activeAdminCount = User::where('role', 'admin')->where('is_active', true)->count();
            if ($activeAdminCount <= 1) {
                return back()->with('error', 'Tidak dapat menonaktifkan admin terakhir yang aktif. Sistem harus memiliki minimal satu admin aktif.');
            }
        }

        $newStatus = !$user->is_active;
        $user->update(['is_active' => $newStatus]);

        $label = $newStatus ? 'mengaktifkan' : 'menonaktifkan';
        AuditLog::catat("{$label} akun user", 'User', $user->id, $user->name . ' (' . $user->email . ')');

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('status', "Akun \"{$user->name}\" berhasil {$statusText}.");
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang digunakan.');
        }

        if ($user->role === 'admin' && $user->is_active) {
            $activeAdminCount = User::where('role', 'admin')->where('is_active', true)->count();
            if ($activeAdminCount <= 1) {
                return back()->with('error', 'Tidak dapat menghapus admin terakhir yang aktif. Sistem harus memiliki minimal satu admin aktif.');
            }
        }

        if ($user->pegawai) {
            $user->pegawai->update(['user_id' => null]);
        }

        AuditLog::catat('menghapus akun user', 'User', $user->id, $user->name . ' (' . $user->email . ')');
        $name = $user->name;
        $user->delete();

        return redirect()->route('user.index')
            ->with('status', "Akun user \"{$name}\" berhasil dihapus.");
    }
}
