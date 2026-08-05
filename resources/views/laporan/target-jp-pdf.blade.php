<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Target Jam Pelajaran (JP)</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #1C1712; margin: 20px; }
        .header { margin-bottom: 20px; border-b: 2px solid #C1272D; padding-bottom: 10px; }
        .title { font-size: 16px; font-weight: bold; color: #C1272D; text-transform: uppercase; margin: 0; }
        .subtitle { font-size: 11px; color: #6B6459; margin-top: 4px; }

        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th, table.data-table td { border: 1px solid #E7E0D2; padding: 6px 8px; text-align: left; font-size: 9px; }
        table.data-table th { background: #0891B2; color: #FFFFFF; font-weight: bold; text-transform: uppercase; }
        table.data-table tr:nth-child(even) { background: #FAF8F5; }

        .badge-success { color: #059669; font-weight: bold; }
        .badge-danger { color: #DC2626; font-weight: bold; }

        .footer { margin-top: 20px; text-align: right; font-size: 8px; color: #6B6459; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">Laporan Capaian Target Jam Pelajaran (JP)</h1>
        <p class="subtitle">Evaluasi Target JP Tahunan (Standar: {{ $targetJpDefault }} JP/Tahun) &bull; Tahun {{ $tahun }} &bull; Dicetak pada {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">NIP</th>
                <th style="width: 25%;">Nama Pegawai</th>
                <th style="width: 20%;">Unit Kerja</th>
                <th style="width: 10%;">Target JP</th>
                <th style="width: 10%;">Capaian JP</th>
                <th style="width: 15%;">Status Target</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reportList as $index => $r)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $r['nip'] }}</td>
                    <td><strong>{{ $r['nama'] }}</strong></td>
                    <td>{{ $r['unit'] }}</td>
                    <td style="text-align: center;">{{ $r['target_jp'] }} JP</td>
                    <td style="text-align: center; font-weight: bold;">{{ $r['capaian_jp'] }} JP</td>
                    <td style="text-align: center;">
                        @if ($r['status'] === 'Tercapai')
                            <span class="badge-success">Tercapai</span>
                        @else
                            <span class="badge-danger">Belum Tercapai ({{ $r['kekurangan'] }} JP)</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 15px; color: #6B6459;">Belum ada data pegawai.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen Resmi SIMPEG-KPI &bull; Modul Monitoring Target Diklat Pegawai
    </div>
</body>
</html>
