<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 9px; color: #15110F; }
        h1 { font-size: 14px; margin-bottom: 4px; }
        p.sub { color: #6B6560; margin-top: 0; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 4px 6px; text-align: left; }
        th { background: #C1272D; color: #fff; font-size: 9px; }
        tr:nth-child(even) { background: #FAF6EF; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Rekap Ketidakhadiran & Kegiatan Non-Cuti — {{ \Carbon\Carbon::parse($bulan.'-01')->translatedFormat('F Y') }}</h1>
    <p class="sub">Dicetak pada {{ now()->translatedFormat('d F Y H:i') }}</p>
    <table>
        <thead>
            <tr>
                <th>NIP</th>
                <th>Nama</th>
                <th>Unit Kerja</th>
                @foreach ($categories as $cat)
                    <th class="text-center">{{ $cat->nama }}</th>
                @endforeach
                <th class="text-center">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pegawais as $p)
                @php
                    $pAbs = $absensis->get($p->id) ?? collect();
                    $total = 0;
                @endphp
                <tr>
                    <td>{{ $p->nip }}</td>
                    <td class="font-bold">{{ $p->nama }}</td>
                    <td>{{ $p->unit?->nama_unit ?? '—' }}</td>
                    @foreach ($categories as $cat)
                        @php
                            $count = $pAbs->where('jenis_ketidakhadiran_id', $cat->id)->count();
                            $total += $count;
                        @endphp
                        <td class="text-center">{{ $count > 0 ? $count : 0 }}</td>
                    @endforeach
                    <td class="text-center font-bold">{{ $total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
