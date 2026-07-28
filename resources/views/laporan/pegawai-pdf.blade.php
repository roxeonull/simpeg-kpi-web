<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #15110F; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        p.sub { color: #6B6560; margin-top: 0; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #C1272D; color: #fff; }
        tr:nth-child(even) { background: #FAF6EF; }
    </style>
</head>
<body>
    <h1>Data Pegawai — SIMPEG-KPI</h1>
    <p class="sub">Dicetak pada {{ now()->format('d F Y H:i') }}</p>
    <table>
        <thead>
            <tr><th>NIP</th><th>Nama</th><th>Jabatan</th><th>Unit Kerja</th><th>Status</th><th>TMT</th><th>Aktif</th></tr>
        </thead>
        <tbody>
            @foreach ($pegawais as $p)
                <tr>
                    <td>{{ $p->nip }}</td>
                    <td>{{ $p->nama }}</td>
                    <td>{{ $p->jabatan?->nama_jabatan }}</td>
                    <td>{{ $p->unit?->nama_unit }}</td>
                    <td>{{ $p->status_kepegawaian }}</td>
                    <td>{{ optional($p->tmt)->format('d-m-Y') }}</td>
                    <td>{{ ucfirst($p->status_aktif) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
