<x-app-layout title="Preview Import Jadwal Shift">
    <div class="max-w-4xl mx-auto" x-data="{ activeStep: 'names' }">
        <a href="{{ route('absensi.shift.import-form', ['shift' => $shift, 'bulan' => $bulan]) }}" class="mb-4 inline-flex items-center gap-1.5 text-sm font-medium text-kpi-gray hover:text-kpi-red">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke upload file
        </a>

        <div class="card">
            <div class="border-b border-kpi-line pb-4 mb-5 dark:border-white/10 flex items-center justify-between">
                <div>
                    <h2 class="font-serif text-xl font-bold">Preview & Mapping Jadwal Shift</h2>
                    <p class="text-xs text-kpi-gray mt-1">Langkah 2 dari 3: Sesuaikan data pegawai dan status shift sebelum disimpan.</p>
                </div>
                <span class="badge bg-kpi-gold-soft text-kpi-red-dark font-bold text-xs">
                    Shift {{ $shift }} &middot; {{ $bulan }}
                </span>
            </div>

            @if ($errors->any())
                <div class="mb-4 rounded-xl bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-800/30 p-4 text-sm text-rose-800 dark:text-rose-300">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($colorDetectionFailed)
                <div class="mb-4 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/40 p-4 text-sm text-amber-900 dark:text-amber-200 flex items-start gap-3">
                    <svg class="h-5 w-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div>
                        <p class="font-bold">Deteksi Warna Sel Excel Tidak Berhasil / Tanpa Warna Highlight</p>
                        <p class="text-xs mt-1">Sistem tidak menemukan warna highlight sel pada file ini. Harap periksa dan sesuaikan pemetaan status secara teliti sebelum menyimpan data.</p>
                    </div>
                </div>
            @endif

            {{-- Override/New Summary Banner --}}
            @if ($overrideCount > 0 || $newCount > 0)
                <div class="mb-4 flex flex-wrap items-center gap-3 rounded-xl border border-kpi-line bg-stone-50 dark:bg-white/5 dark:border-white/10 px-4 py-3 text-sm">
                    <svg class="h-4 w-4 shrink-0 text-kpi-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-kpi-gray font-medium">Ringkasan data yang akan disimpan:</span>
                    @if ($newCount > 0)
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 dark:bg-emerald-500/15 px-2.5 py-0.5 text-xs font-bold text-emerald-700 dark:text-emerald-400">
                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/></svg>
                            {{ $newCount }} entry baru
                        </span>
                    @endif
                    @if ($overrideCount > 0)
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 dark:bg-amber-500/15 px-2.5 py-0.5 text-xs font-bold text-amber-700 dark:text-amber-400">
                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $overrideCount }} akan menimpa data lama
                        </span>
                    @endif
                </div>
            @endif

            {{-- Step Navigation Tabs --}}
            <div class="mb-5 flex gap-2 border-b border-kpi-line dark:border-white/10">
                <button type="button" @click="activeStep = 'names'" :class="activeStep === 'names' ? 'border-kpi-red text-kpi-red font-semibold' : 'border-transparent text-kpi-gray hover:text-kpi-black'" class="border-b-2 px-4 py-2.5 text-sm font-medium transition-colors">
                    1. Pemetaan Pegawai ({{ count($nameMappings) }} nama)
                </button>
                <button type="button" @click="activeStep = 'status'" :class="activeStep === 'status' ? 'border-kpi-red text-kpi-red font-semibold' : 'border-transparent text-kpi-gray hover:text-kpi-black'" class="border-b-2 px-4 py-2.5 text-sm font-medium transition-colors">
                    2. Pemetaan Status Shift ({{ count($statusMappings) }} kode)
                </button>
                @if (count($previewRows) > 0)
                <button type="button" @click="activeStep = 'preview'" :class="activeStep === 'preview' ? 'border-kpi-red text-kpi-red font-semibold' : 'border-transparent text-kpi-gray hover:text-kpi-black'" class="border-b-2 px-4 py-2.5 text-sm font-medium transition-colors">
                    3. Pratinjau Data ({{ count($previewRows) }} baris)
                </button>
                @endif
            </div>

            <form method="POST" action="{{ route('absensi.shift.import-commit') }}">
                @csrf

                {{-- SECTION 1: NAMES MAPPING --}}
                <div x-show="activeStep === 'names'" class="space-y-4">
                    <p class="text-xs text-kpi-gray mb-3">
                        Berikut adalah nama-nama pegawai yang ditemukan di file Excel. Silakan periksa atau ubah pemetaan ke pegawai database yang sesuai.
                    </p>

                    <div class="table-shell max-h-[500px] overflow-y-auto pr-1">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b border-kpi-line bg-kpi-cream/60 dark:border-white/10 dark:bg-white/[0.03] sticky top-0 z-10">
                                <tr>
                                    <th class="th p-3">Nama di Excel</th>
                                    <th class="th p-3">Pemetaan Pegawai</th>
                                    <th class="th p-3">Info / NIP Pegawai Baru</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-kpi-line dark:divide-white/5">
                                @foreach ($nameMappings as $map)
                                    <tr x-data="{ mapType: '{{ $map['matched_pegawai_id'] ?? '' }}' }" class="tr-hover">
                                        <td class="td p-3 font-semibold text-stone-700 dark:text-stone-300">
                                            {{ $map['parsed_name'] }}
                                        </td>
                                        <td class="td p-3 min-w-[250px]">
                                            <select name="name_mapping[{{ $map['parsed_name'] }}]" x-model="mapType" class="input w-full">
                                                <option value="">-- Abaikan (Jangan Import) --</option>
                                                <option value="new">+ Buat Pegawai Baru</option>
                                                @foreach($allPegawais as $p)
                                                    <option value="{{ $p->id }}">{{ $p->nama }} (NIP: {{ $p->nip }})</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="td p-3">
                                            <div x-show="mapType === 'new'" x-cloak class="space-y-1">
                                                <input type="text" name="new_nip[{{ $map['parsed_name'] }}]" placeholder="Input NIP baru" :required="mapType === 'new'" class="input w-full py-1 text-xs">
                                                <p class="text-[9px] text-kpi-gray">Nama baru akan disamakan dengan nama di Excel.</p>
                                            </div>
                                            <div x-show="mapType !== 'new' && mapType !== ''" x-cloak class="text-xs text-emerald-600 font-semibold flex items-center gap-1">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Terpetakan
                                            </div>
                                            <div x-show="mapType === ''" x-cloak class="text-xs text-rose-500 font-semibold flex items-center gap-1">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Diabaikan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-between border-t border-kpi-line pt-4 dark:border-white/10 mt-6">
                        <span></span>
                        <button type="button" @click="activeStep = 'status'" class="btn-primary flex items-center gap-2">
                            Lanjut ke Pemetaan Status
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>

                {{-- SECTION 2: STATUS MAPPING --}}
                <div x-show="activeStep === 'status'" x-cloak class="space-y-4">
                    <p class="text-xs text-kpi-gray mb-3">
                        Berikut adalah kode-kode khusus yang ditemukan pada tanggal-tanggal libur/absen di Excel. Petakan kode-kode tersebut ke Status Shift Master Data.
                    </p>

                    <div class="table-shell overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b border-kpi-line bg-kpi-cream/60 dark:border-white/10 dark:bg-white/[0.03]">
                                <tr>
                                    <th class="th p-3">Kode di Excel</th>
                                    <th class="th p-3">Pemetaan Status Shift</th>
                                    <th class="th p-3">Keterangan Status Baru</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-kpi-line dark:divide-white/5">
                                @forelse ($statusMappings as $map)
                                    <tr x-data="{ statusType: '{{ $map['matched_status_id'] ?? 'new' }}' }" class="tr-hover">
                                        <td class="td p-3 font-mono font-bold text-base text-stone-700 dark:text-stone-300">
                                            {{ $map['code'] }}
                                        </td>
                                        <td class="td p-3 min-w-[250px]">
                                            <select name="status_mapping[{{ $map['code'] }}]" x-model="statusType" class="input w-full">
                                                <option value="new">+ Buat Status Shift Baru</option>
                                                @foreach($dbStatusShifts as $s)
                                                    <option value="{{ $s->id }}">{{ $s->nama }} ({{ $s->kode }})</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="td p-3">
                                            <div x-show="statusType === 'new'" x-cloak class="flex flex-col gap-2">
                                                <input type="text" name="new_status_nama[{{ $map['code'] }}]" placeholder="Nama Status (Cuti Bersama)" :required="statusType === 'new'" class="input py-1 text-xs">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs text-kpi-gray">Warna label:</span>
                                                    <input type="color" name="new_status_warna[{{ $map['code'] }}]" value="{{ $map['proposed_color'] ?? '#fca5a5' }}" class="h-8 w-10 p-0.5 rounded border border-kpi-line bg-transparent cursor-pointer">
                                                </div>
                                            </div>
                                            <div x-show="statusType !== 'new'" x-cloak class="text-xs text-emerald-600 font-semibold flex items-center gap-1">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Terpetakan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="p-6 text-center text-kpi-gray">
                                            Tidak ditemukan kode status/libur khusus (semua pegawai bekerja normal masuk shift).
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-between border-t border-kpi-line pt-4 dark:border-white/10 mt-6">
                        <button type="button" @click="activeStep = 'names'" class="btn-secondary flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Kembali ke Pemetaan Pegawai
                        </button>
                        @if (count($previewRows) > 0)
                        <button type="button" @click="activeStep = 'preview'" class="btn-primary flex items-center gap-2">
                            Lihat Pratinjau Data
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                        @else
                        <button type="submit" class="btn-primary flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan & Komit Jadwal Shift
                        </button>
                        @endif
                    </div>
                </div>

                {{-- SECTION 3: DATA PREVIEW --}}
                @if (count($previewRows) > 0)
                <div x-show="activeStep === 'preview'" x-cloak class="space-y-4">
                    <p class="text-xs text-kpi-gray mb-3">
                        Berikut adalah semua baris data yang akan disimpan ke database. Baris dengan latar <span class="font-semibold text-amber-700 dark:text-amber-400">kuning</span> akan <strong>menimpa data lama</strong>, sedangkan baris dengan latar <span class="font-semibold text-emerald-700 dark:text-emerald-400">hijau</span> adalah entry <strong>baru</strong>.
                    </p>

                    <div class="flex gap-3 mb-3">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 dark:bg-emerald-500/15 px-3 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-400">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            Entry Baru ({{ $newCount }})
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 dark:bg-amber-500/15 px-3 py-1 text-xs font-semibold text-amber-700 dark:text-amber-400">
                            <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                            Akan Menimpa Data Lama ({{ $overrideCount }})
                        </span>
                    </div>

                    <div class="table-shell max-h-[500px] overflow-y-auto pr-1">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b border-kpi-line bg-kpi-cream/60 dark:border-white/10 dark:bg-white/[0.03] sticky top-0 z-10">
                                <tr>
                                    <th class="th p-2.5 text-xs">Pegawai</th>
                                    <th class="th p-2.5 text-xs">Tanggal</th>
                                    <th class="th p-2.5 text-xs">Nilai / Status</th>
                                    <th class="th p-2.5 text-xs">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-kpi-line dark:divide-white/5">
                                @foreach ($previewRows as $row)
                                    <tr class="{{ $row['is_override'] ? 'bg-amber-50/60 dark:bg-amber-500/5' : 'bg-emerald-50/40 dark:bg-emerald-500/5' }}">
                                        <td class="td p-2.5 font-medium text-stone-700 dark:text-stone-300 text-xs">{{ $row['nama'] }}</td>
                                        <td class="td p-2.5 font-mono text-xs text-kpi-gray">{{ $row['tanggal'] }}</td>
                                        <td class="td p-2.5 text-xs">
                                            @if ($row['is_status'])
                                                <span class="inline-flex items-center rounded-full bg-rose-100 dark:bg-rose-500/15 px-2 py-0.5 text-xs font-bold text-rose-700 dark:text-rose-400">
                                                    {{ $row['status_code'] }}
                                                </span>
                                            @elseif (!empty($row['cell_value']))
                                                <span class="text-stone-600 dark:text-stone-300">{{ $row['cell_value'] }}</span>
                                            @else
                                                <span class="text-kpi-gray italic text-[11px]">—</span>
                                            @endif
                                        </td>
                                        <td class="td p-2.5">
                                            @if ($row['is_override'])
                                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 dark:bg-amber-500/15 px-2 py-0.5 text-[10px] font-semibold text-amber-700 dark:text-amber-400">
                                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                                    Akan menimpa data lama
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 dark:bg-emerald-500/15 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 dark:text-emerald-400">
                                                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/></svg>
                                                    Entry baru
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-between border-t border-kpi-line pt-4 dark:border-white/10 mt-6">
                        <button type="button" @click="activeStep = 'status'" class="btn-secondary flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Kembali ke Pemetaan Status
                        </button>
                        <button type="submit" class="btn-primary flex items-center gap-2">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan & Komit Jadwal Shift
                        </button>
                    </div>
                </div>
                @endif

            </form>
        </div>
    </div>
</x-app-layout>
