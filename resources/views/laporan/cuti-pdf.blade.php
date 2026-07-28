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
    <h1>Rekap Cuti — Tahun {{ $tahun }}</h1>
    <p class="sub">Dicetak pada {{ now()->format('d F Y H:i') }}</p>
    <table>
        <thead>
            <tr><th>NIP</th><th>Nama</th><th>Jenis</th><th>Mulai</th><th>Selesai</th><th>Hari</th><th>Status</th></tr>
        </thead>
        <tbody>
            @foreach ($cutis as $c)
                <tr>
                    <td>{{ $c->pegawai?->nip }}</td>
                    <td>{{ $c->pegawai?->nama }}</td>
                    <td>{{ ucfirst($c->jenis_cuti) }}</td>
                    <td>{{ $c->tanggal_mulai->format('d-m-Y') }}</td>
                    <td>{{ $c->tanggal_selesai->format('d-m-Y') }}</td>
                    <td>{{ $c->jumlah_hari }}</td>
                    <td>{{ $c->statusLabel() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
