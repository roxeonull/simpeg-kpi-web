<x-app-layout :title="$user->exists ? 'Edit User' : 'Tambah User'">
    <div class="max-w-2xl space-y-6">

        {{-- Header --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('user.index') }}" class="flex h-8 w-8 items-center justify-center rounded-lg border border-kpi-line text-kpi-gray hover:border-kpi-red hover:text-kpi-red transition-colors dark:border-white/10">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="font-serif text-lg font-bold text-kpi-black dark:text-stone-50">
                    {{ $user->exists ? 'Edit Akun User' : 'Tambah Akun User Baru' }}
                </h2>
                <p class="text-xs text-kpi-gray">
                    {{ $user->exists ? 'Perbarui informasi akun login.' : 'Buat akun login baru untuk sistem SIMPEG-KPI.' }}
                </p>
            </div>
        </div>

        {{-- Form --}}
        <form method="POST"
              action="{{ $user->exists ? route('user.update', $user) : route('user.store') }}"
              x-data="{ submitting: false }"
              @submit="submitting = true"
              class="space-y-5">
            @csrf
            @if($user->exists) @method('PUT') @endif

            {{-- Nama --}}
            <div class="card space-y-4">
                <h3 class="font-semibold text-sm text-kpi-black dark:text-stone-100">Informasi Akun</h3>

                <div>
                    <label for="name" class="label">Nama Lengkap <span class="text-kpi-red">*</span></label>
                    <input type="text" id="name" name="name"
                           value="{{ old('name', $user->name) }}"
                           class="input mt-1 w-full @error('name') border-kpi-red @enderror"
                           required placeholder="Nama lengkap pengguna">
                    @error('name')
                        <p class="mt-1 text-xs text-kpi-red">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="label">Email <span class="text-kpi-red">*</span></label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email', $user->email) }}"
                           class="input mt-1 w-full @error('email') border-kpi-red @enderror"
                           required placeholder="email@kpi.go.id">
                    @error('email')
                        <p class="mt-1 text-xs text-kpi-red">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="label">
                        Password <span class="text-kpi-red">*</span>
                        @if($user->exists)
                            <span class="ml-1 text-[11px] font-normal text-kpi-gray">(kosongkan jika tidak ingin mengubah)</span>
                        @endif
                    </label>
                    <input type="password" id="password" name="password"
                           class="input mt-1 w-full @error('password') border-kpi-red @enderror"
                           {{ $user->exists ? '' : 'required' }}
                           minlength="8"
                           placeholder="{{ $user->exists ? '••••••••' : 'Minimal 8 karakter' }}">
                    @error('password')
                        <p class="mt-1 text-xs text-kpi-red">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Role & Status --}}
            <div class="card space-y-4 relative z-20">
                <h3 class="font-semibold text-sm text-kpi-black dark:text-stone-100">Hak Akses & Status</h3>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="role" class="label">Role <span class="text-kpi-red">*</span></label>
                        <x-select id="role" name="role"
                            :value="old('role', $user->role ?? '')"
                            :options="[
                                ['value' => '', 'label' => '— Pilih Role —'],
                                ['value' => 'admin', 'label' => 'Admin'],
                                ['value' => 'atasan', 'label' => 'Atasan'],
                                ['value' => 'pegawai', 'label' => 'Pegawai'],
                            ]"
                            class="mt-1 w-full @error('role') border-kpi-red @enderror" />
                        @error('role')
                            <p class="mt-1 text-xs text-kpi-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="is_active" class="label">Status Akun <span class="text-kpi-red">*</span></label>
                        <x-select id="is_active" name="is_active"
                            :value="old('is_active', $user->exists ? (string)(int)$user->is_active : '1')"
                            :options="[
                                ['value' => '1', 'label' => 'Aktif'],
                                ['value' => '0', 'label' => 'Nonaktif'],
                            ]"
                            class="mt-1 w-full @error('is_active') border-kpi-red @enderror" />
                        @error('is_active')
                            <p class="mt-1 text-xs text-kpi-red">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Tautan Pegawai --}}
            <div class="card space-y-3 relative z-10">
                <div>
                    <h3 class="font-semibold text-sm text-kpi-black dark:text-stone-100">Tautan Data Pegawai</h3>
                    <p class="text-xs text-kpi-gray mt-0.5">Opsional — pilih pegawai yang akan dihubungkan ke akun ini.</p>
                </div>

                @php
                    $pegawaiOptions = [['value' => '', 'label' => '— Tidak ditautkan —']];
                    foreach ($pegawaiTanpaAkun as $p) {
                        $pegawaiOptions[] = ['value' => (string)$p->id, 'label' => $p->nama . ($p->nip ? ' — ' . $p->nip : '')];
                    }
                    $selectedPegawai = old('pegawai_id', (string)($preselectedId ?? ''));
                @endphp

                <div>
                    <label for="pegawai_id" class="label">Data Pegawai</label>
                    <x-select-search id="pegawai_id" name="pegawai_id"
                        :value="$selectedPegawai"
                        :options="$pegawaiOptions"
                        placeholder="Cari pegawai..."
                        class="mt-1 w-full @error('pegawai_id') border-kpi-red @enderror" />
                    @error('pegawai_id')
                        <p class="mt-1 text-xs text-kpi-red">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-[11px] text-kpi-gray">
                        Hanya menampilkan pegawai yang belum memiliki akun login.
                        @if($user->exists && $user->pegawai)
                            Pegawai yang saat ini tertaut (<strong>{{ $user->pegawai->nama }}</strong>) juga ditampilkan.
                        @endif
                    </p>
                </div>
            </div>

            {{-- Tombol Simpan --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('user.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" :disabled="submitting" class="btn-primary flex items-center gap-2">
                    <svg x-show="!submitting" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <svg x-show="submitting" x-cloak class="h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="submitting ? 'Menyimpan...' : '{{ $user->exists ? 'Simpan Perubahan' : 'Buat Akun' }}'"></span>
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
