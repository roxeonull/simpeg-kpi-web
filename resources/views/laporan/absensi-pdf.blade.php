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
    <h1>Rekap Absensi — {{ \Carbon\Carbon::parse($bulan.'-01')->translatedFormat('F Y') }}</h1>
    <p class="sub">Dicetak pada {{ now()->format('d F Y H:i') }}</p>
    <table>
        <thead>
            <tr><th>Tanggal</th><th>NIP</th><th>Nama</th><th>Jam Masuk</th><th>Jam Keluar</th><th>Status</th><th>Keterangan</th></tr>
        </thead>
        <tbody>
            @foreach ($absensis as $a)
                <tr>
                    <td>{{ $a->tanggal->format('d-m-Y') }}</td>
                    <td>{{ $a->pegawai?->nip }}</td>
                    <td>{{ $a->pegawai?->nama }}</td>
                    <td>{{ $a->jam_masuk }}</td>
                    <td>{{ $a->jam_keluar }}</td>
                    <td>{{ ucfirst($a->status) }}</td>
                    <td>{{ $a->keterangan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
