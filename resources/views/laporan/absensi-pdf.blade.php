<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi Pegawai</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #1C1712; margin: 20px; }
        .header { margin-bottom: 20px; border-b: 2px solid #C1272D; padding-bottom: 10px; }
        .title { font-size: 16px; font-weight: bold; color: #C1272D; text-transform: uppercase; margin: 0; }
        .subtitle { font-size: 11px; color: #6B6459; margin-top: 4px; }

        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th, table.data-table td { border: 1px solid #E7E0D2; padding: 6px 8px; text-align: left; font-size: 9px; }
        table.data-table th { background: #059669; color: #FFFFFF; font-weight: bold; text-transform: uppercase; }
        table.data-table tr:nth-child(even) { background: #FAF8F5; }

        .footer { margin-top: 20px; text-align: right; font-size: 8px; color: #6B6459; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">{{ !empty($isDetail) ? 'Detail Absensi Log Harian' : 'Rekapitulasi Presensi Bulanan Pegawai' }}</h1>
        <p class="subtitle">Bulan {{ \Carbon\Carbon::parse($bulan.'-01')->translatedFormat('F Y') }} &bull; Dicetak pada {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    @if (!empty($isDetail))
        {{-- Detail Timesheet Harian --}}
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 15%;">NIP</th>
                    <th style="width: 22%;">Nama Pegawai</th>
                    <th style="width: 10%;">Jam Masuk</th>
                    <th style="width: 10%;">Jam Keluar</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 14%;">Potongan (Menit)</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($absensis as $index => $a)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>{{ $a->tanggal ? $a->tanggal->format('d/m/Y') : '-' }}</td>
                        <td>{{ optional($a->pegawai)->nip ?? '-' }}</td>
                        <td><strong>{{ optional($a->pegawai)->nama ?? '-' }}</strong></td>
                        <td style="text-align: center;">{{ $a->jam_masuk ? substr($a->jam_masuk, 0, 5) : '-' }}</td>
                        <td style="text-align: center;">{{ $a->jam_keluar ? substr($a->jam_keluar, 0, 5) : '-' }}</td>
                        <td style="text-align: center;">{{ ucfirst($a->status) }}</td>
                        <td style="text-align: center; color: {{ ($a->menit_pengurangan_jam_kerja ?? 0) > 0 ? '#DC2626' : '#1C1712' }}; font-weight: bold;">
                            {{ $a->menit_pengurangan_jam_kerja ?? 0 }} mnt
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 15px; color: #6B6459;">Tidak ada data log absensi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @else
        {{-- Ringkasan Rekap Presensi Per Pegawai --}}
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">NIP</th>
                    <th style="width: 25%;">Nama Pegawai</th>
                    <th style="width: 20%;">Unit Kerja</th>
                    <th style="width: 7%;">Hadir</th>
                    <th style="width: 8%;">Terlambat</th>
                    <th style="width: 7%;">Izin</th>
                    <th style="width: 6%;">Alpa</th>
                    <th style="width: 12%;">Total Potongan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($summaryList as $index => $s)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>{{ $s['nip'] }}</td>
                        <td><strong>{{ $s['nama'] }}</strong></td>
                        <td>{{ $s['unit'] }}</td>
                        <td style="text-align: center; color: #059669; font-weight: bold;">{{ $s['hadir'] }}</td>
                        <td style="text-align: center; color: #D97706;">{{ $s['terlambat'] }}</td>
                        <td style="text-align: center; color: #2563EB;">{{ $s['izin'] }}</td>
                        <td style="text-align: center; color: #DC2626;">{{ $s['alpa'] }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $s['potongan_menit'] }} mnt</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 15px; color: #6B6459;">Belum ada data absensi pegawai bulan ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <div class="footer">
        Dokumen Resmi SIMPEG-KPI &bull; Modul Rekapitulasi Presensi Pegawai
    </div>
</body>
</html>
