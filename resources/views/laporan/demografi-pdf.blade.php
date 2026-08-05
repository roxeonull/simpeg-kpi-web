<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Demografi Pegawai</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #1C1712; margin: 20px; }
        .header { margin-bottom: 20px; border-b: 2px solid #C1272D; padding-bottom: 10px; }
        .title { font-size: 16px; font-weight: bold; color: #C1272D; text-transform: uppercase; margin: 0; }
        .subtitle { font-size: 11px; color: #6B6459; margin-top: 4px; }
        
        .stat-grid { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .stat-card { background: #F7F3EA; border: 1px solid #E7E0D2; padding: 10px; text-align: center; border-radius: 6px; }
        .stat-number { font-size: 18px; font-weight: bold; color: #C1272D; }
        .stat-label { font-size: 9px; color: #6B6459; text-transform: uppercase; margin-top: 2px; }

        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th, table.data-table td { border: 1px solid #E7E0D2; padding: 6px 8px; text-align: left; font-size: 9px; }
        table.data-table th { background: #C1272D; color: #FFFFFF; font-weight: bold; text-transform: uppercase; }
        table.data-table tr:nth-child(even) { background: #FAF8F5; }

        .footer { margin-top: 20px; text-align: right; font-size: 8px; color: #6B6459; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">Laporan Demografi Pegawai</h1>
        <p class="subtitle">Rekapitulasi Usia, Pendidikan, Jenis Kelamin, dan Status Kepegawaian &bull; Dicetak pada {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    {{-- Ringkasan Statistik Demografi --}}
    <table class="stat-grid">
        <tr>
            <td style="width: 25%; padding: 4px;">
                <div class="stat-card">
                    <div class="stat-number">{{ $totalPegawai }}</div>
                    <div class="stat-label">Total Pegawai</div>
                </div>
            </td>
            <td style="width: 25%; padding: 4px;">
                <div class="stat-card">
                    <div class="stat-number">{{ $totalLaki }} / {{ $totalPerempuan }}</div>
                    <div class="stat-label">Laki-Laki / Perempuan</div>
                </div>
            </td>
            <td style="width: 25%; padding: 4px;">
                <div class="stat-card">
                    <div class="stat-number">{{ $rataRataUsia }} Thn</div>
                    <div class="stat-label">Rata-Rata Usia</div>
                </div>
            </td>
            <td style="width: 25%; padding: 4px;">
                <div class="stat-card">
                    <div class="stat-number">{{ $pNSCount }} / {{ $nonPNSCount }}</div>
                    <div class="stat-label">PNS / Non-PNS</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 12%;">NIP</th>
                <th style="width: 20%;">Nama Pegawai</th>
                <th style="width: 6%;">L/P</th>
                <th style="width: 12%;">Tempat, Tgl Lahir</th>
                <th style="width: 6%;">Usia</th>
                <th style="width: 14%;">Pendidikan / Jurusan</th>
                <th style="width: 14%;">Unit Kerja</th>
                <th style="width: 12%;">Status Kepegawaian</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pegawais as $index => $p)
                @php
                    $usia = $p->tanggal_lahir ? \Carbon\Carbon::parse($p->tanggal_lahir)->age . ' thn' : '-';
                    $tglLahir = $p->tanggal_lahir ? \Carbon\Carbon::parse($p->tanggal_lahir)->format('d/m/Y') : '';
                    $ttl = trim(($p->tempat_lahir ? $p->tempat_lahir . ', ' : '') . $tglLahir, ', ');
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $p->nip ?? '-' }}</td>
                    <td><strong>{{ $p->nama }}</strong></td>
                    <td style="text-align: center;">{{ strtoupper($p->jenis_kelamin ?? '-') }}</td>
                    <td>{{ $ttl ?: '-' }}</td>
                    <td style="text-align: center;">{{ $usia }}</td>
                    <td>{{ $p->pendidikan_terakhir ?? '-' }} {{ $p->jurusan_pendidikan ? "({$p->jurusan_pendidikan})" : '' }}</td>
                    <td>{{ optional($p->unit)->nama_unit ?? '-' }}</td>
                    <td>{{ $p->status_kepegawaian ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 15px; color: #6B6459;">Tidak ada data pegawai.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen resmi SIMPEG-KPI &bull; Sistem Informasi Manajemen Kepegawaian
    </div>
</body>
</html>
