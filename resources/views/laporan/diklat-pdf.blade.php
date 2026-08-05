<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Diklat & Pelatihan Pegawai</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #1C1712; margin: 20px; }
        .header { margin-bottom: 20px; border-b: 2px solid #C1272D; padding-bottom: 10px; }
        .title { font-size: 16px; font-weight: bold; color: #C1272D; text-transform: uppercase; margin: 0; }
        .subtitle { font-size: 11px; color: #6B6459; margin-top: 4px; }

        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th, table.data-table td { border: 1px solid #E7E0D2; padding: 6px 8px; text-align: left; font-size: 9px; }
        table.data-table th { background: #6B21A8; color: #FFFFFF; font-weight: bold; text-transform: uppercase; }
        table.data-table tr:nth-child(even) { background: #FAF8F5; }

        .footer { margin-top: 20px; text-align: right; font-size: 8px; color: #6B6459; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">Rekap Diklat & Pelatihan Pegawai</h1>
        <p class="subtitle">Tahun {{ $tahun }} &bull; Dicetak pada {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 12%;">NIP</th>
                <th style="width: 18%;">Nama Pegawai</th>
                <th style="width: 15%;">Unit Kerja</th>
                <th style="width: 22%;">Nama Pelatihan / Diklat</th>
                <th style="width: 15%;">Penyelenggara</th>
                <th style="width: 8%;">Tanggal</th>
                <th style="width: 6%;">JP</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pelatihans as $index => $p)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ optional($p->pegawai)->nip ?? '-' }}</td>
                    <td><strong>{{ optional($p->pegawai)->nama ?? '-' }}</strong></td>
                    <td>{{ optional(optional($p->pegawai)->unit)->nama_unit ?? '-' }}</td>
                    <td>{{ $p->nama_pelatihan }}</td>
                    <td>{{ $p->penyelenggara ?? '-' }}</td>
                    <td>{{ $p->tanggal ? $p->tanggal->format('d/m/Y') : '-' }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $p->durasi_jp ?? 0 }} JP</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 15px; color: #6B6459;">Belum ada data diklat terdaftar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen Resmi SIMPEG-KPI &bull; Modul Diklat & Pengembangan Kompetensi
    </div>
</body>
</html>
