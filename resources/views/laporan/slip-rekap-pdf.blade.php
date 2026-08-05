<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Rekapitulasi Individu Pegawai</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #1C1712; margin: 15px; }
        .page { page-break-after: always; }
        .page:last-child { page-break-after: avoid; }

        .header { border-b: 2px solid #C1272D; padding-bottom: 8px; margin-bottom: 12px; }
        .title { font-size: 16px; font-weight: bold; color: #C1272D; text-transform: uppercase; margin: 0; }
        .subtitle { font-size: 10px; color: #6B6459; margin-top: 3px; }

        .section-title { font-size: 11px; font-weight: bold; color: #C1272D; border-bottom: 1px solid #E7E0D2; padding-bottom: 4px; margin-top: 15px; margin-bottom: 8px; text-transform: uppercase; }

        table.info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.info-table td { padding: 4px 6px; font-size: 9.5px; vertical-align: top; }
        table.info-table td.label { font-weight: bold; color: #6B6459; width: 18%; }
        table.info-table td.value { width: 32%; color: #1C1712; }

        .stat-grid { width: 100%; margin-bottom: 10px; border-collapse: collapse; }
        .stat-card { background: #FAF8F5; border: 1px solid #E7E0D2; padding: 8px; text-align: center; border-radius: 4px; }
        .stat-num { font-size: 15px; font-weight: bold; color: #1C1712; }
        .stat-lbl { font-size: 8.5px; color: #6B6459; text-transform: uppercase; margin-top: 2px; }

        table.data-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.data-table th, table.data-table td { border: 1px solid #E7E0D2; padding: 5px 7px; text-align: left; font-size: 8.5px; }
        table.data-table th { background: #F7F3EA; color: #1C1712; font-weight: bold; text-transform: uppercase; }

        .footer { margin-top: 25px; text-align: right; font-size: 8px; color: #6B6459; }
    </style>
</head>
<body>
    @foreach ($dataPegawaiList as $item)
        @php
            $p = $item['pegawai'];
            $saldo = $item['saldoCuti'];
            $cutis = $item['cutiList'];
            $pelatihans = $item['pelatihanList'];
            $abs = $item['absensiSummary'];
        @endphp
        <div class="page">
            <div class="header">
                <h1 class="title">Slip Rekapitulasi Individu Pegawai</h1>
                <p class="subtitle">Dokumen Profil & Rekap Kinerja Pegawai &bull; Dicetak pada {{ now()->translatedFormat('d F Y H:i') }}</p>
            </div>

            {{-- 1. Identitas Pegawai --}}
            <div class="section-title">1. Identitas & Informasi Pegawai</div>
            <table class="info-table">
                <tr>
                    <td class="label">Nama Lengkap</td>
                    <td class="value">: <strong>{{ $p->nama }}</strong></td>
                    <td class="label">Unit Kerja</td>
                    <td class="value">: {{ optional($p->unit)->nama_unit ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">NIP</td>
                    <td class="value">: {{ $p->nip ?? '-' }}</td>
                    <td class="label">Jabatan</td>
                    <td class="value">: {{ optional($p->jabatan)->nama_jabatan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Status Kepegawaian</td>
                    <td class="value">: {{ $p->status_kepegawaian ?? '-' }}</td>
                    <td class="label">TMT Kepegawaian</td>
                    <td class="value">: {{ $p->tmt ? \Carbon\Carbon::parse($p->tmt)->format('d F Y') : '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Pendidikan Terakhir</td>
                    <td class="value">: {{ $p->pendidikan_terakhir ?? '-' }} {{ $p->jurusan_pendidikan ? "({$p->jurusan_pendidikan})" : '' }}</td>
                    <td class="label">No. Telepon / Email</td>
                    <td class="value">: {{ $p->no_hp ?? '-' }} / {{ $p->email ?? '-' }}</td>
                </tr>
            </table>

            {{-- 2. Rekapitulasi Presensi --}}
            <div class="section-title">2. Rekapitulasi Presensi & Kehadiran</div>
            <table class="stat-grid">
                <tr>
                    <td style="width: 20%; padding: 3px;">
                        <div class="stat-card">
                            <div class="stat-num" style="color: #059669;">{{ $abs['hadir'] }}</div>
                            <div class="stat-lbl">Total Hadir</div>
                        </div>
                    </td>
                    <td style="width: 20%; padding: 3px;">
                        <div class="stat-card">
                            <div class="stat-num" style="color: #D97706;">{{ $abs['terlambat'] }}</div>
                            <div class="stat-lbl">Terlambat</div>
                        </div>
                    </td>
                    <td style="width: 20%; padding: 3px;">
                        <div class="stat-card">
                            <div class="stat-num" style="color: #2563EB;">{{ $abs['izin'] }}</div>
                            <div class="stat-lbl">Izin / Sakit</div>
                        </div>
                    </td>
                    <td style="width: 20%; padding: 3px;">
                        <div class="stat-card">
                            <div class="stat-num" style="color: #DC2626;">{{ $abs['alpa'] }}</div>
                            <div class="stat-lbl">Alpa</div>
                        </div>
                    </td>
                    <td style="width: 20%; padding: 3px;">
                        <div class="stat-card">
                            <div class="stat-num" style="color: #B91C1C;">{{ $abs['potongan_menit'] }} mnt</div>
                            <div class="stat-lbl">Potongan Menit</div>
                        </div>
                    </td>
                </tr>
            </table>

            {{-- 3. Rekapitulasi Cuti --}}
            <div class="section-title">3. Rekapitulasi Saldo & Riwayat Cuti</div>
            <table class="info-table" style="margin-bottom: 5px;">
                <tr>
                    <td class="label">Kuota Saldo Cuti</td>
                    <td class="value">: <strong>{{ $saldo?->total_saldo ?? 12 }} Hari</strong></td>
                    <td class="label">Sisa Saldo Cuti</td>
                    <td class="value">: <strong style="color: #059669;">{{ $saldo?->sisa_saldo ?? 12 }} Hari</strong></td>
                </tr>
            </table>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 25%;">Jenis Cuti</th>
                        <th style="width: 35%;">Periode Tanggal</th>
                        <th style="width: 15%;">Jumlah Hari</th>
                        <th style="width: 20%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cutis as $idx => $c)
                        <tr>
                            <td style="text-align: center;">{{ $idx + 1 }}</td>
                            <td>{{ optional($c->jenisCuti)->nama ?? ucfirst($c->jenis_cuti) }}</td>
                            <td>{{ $c->tanggal_mulai ? $c->tanggal_mulai->format('d/m/Y') : '' }} s/d {{ $c->tanggal_selesai ? $c->tanggal_selesai->format('d/m/Y') : '' }}</td>
                            <td style="text-align: center;">{{ $c->jumlah_hari }} Hari</td>
                            <td>{{ $c->statusLabel() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #6B6459; padding: 6px;">Belum ada riwayat pengajuan cuti.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- 4. Rekapitulasi Diklat --}}
            <div class="section-title">4. Riwayat Pelatihan & Pengembangan Kompetensi (Diklat)</div>
            <table class="info-table" style="margin-bottom: 5px;">
                <tr>
                    <td class="label">Total Capaian JP</td>
                    <td class="value">: <strong style="color: #2563EB;">{{ $item['totalJp'] }} JP</strong></td>
                    <td class="label">Total Diklat Diikuti</td>
                    <td class="value">: {{ $pelatihans->count() }} Kegiatan</td>
                </tr>
            </table>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 35%;">Nama Pelatihan / Diklat</th>
                        <th style="width: 25%;">Penyelenggara</th>
                        <th style="width: 15%;">Tanggal</th>
                        <th style="width: 20%;">Durasi (JP)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pelatihans as $idx => $pel)
                        <tr>
                            <td style="text-align: center;">{{ $idx + 1 }}</td>
                            <td>{{ $pel->nama_pelatihan }}</td>
                            <td>{{ $pel->penyelenggara ?? '-' }}</td>
                            <td>{{ $pel->tanggal ? $pel->tanggal->format('d/m/Y') : '-' }}</td>
                            <td style="text-align: center; font-weight: bold;">{{ $pel->durasi_jp ?? 0 }} JP</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #6B6459; padding: 6px;">Belum ada riwayat pelatihan terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="footer">
                Dokumen Resmi SIMPEG-KPI &bull; Slip Rekapitulasi Kinerja Pegawai
            </div>
        </div>
    @endforeach
</body>
</html>
