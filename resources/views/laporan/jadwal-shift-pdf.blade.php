<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Jadwal Shift Bulanan</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 8px; color: #1C1712; margin: 10px; }
        .header { margin-bottom: 10px; border-b: 2px solid #C1272D; padding-bottom: 5px; }
        .title { font-size: 14px; font-weight: bold; color: #C1272D; text-transform: uppercase; margin: 0; }
        .subtitle { font-size: 10px; color: #6B6459; margin-top: 2px; }

        .legend { margin-bottom: 8px; font-size: 8px; color: #6B6459; }
        .legend-item { display: inline-block; margin-right: 12px; }

        table.matrix-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.matrix-table th, table.matrix-table td { border: 1px solid #D6D1C7; padding: 3px 1px; text-align: center; font-size: 7.5px; overflow: hidden; }
        table.matrix-table th { background: #2563EB; color: #FFFFFF; font-weight: bold; }
        table.matrix-table th.col-pegawai { text-align: left; padding-left: 4px; width: 14%; }
        table.matrix-table td.col-pegawai { text-align: left; padding-left: 4px; font-weight: bold; }
        table.matrix-table tr:nth-child(even) { background: #FAF8F5; }

        .footer { margin-top: 15px; text-align: right; font-size: 8px; color: #6B6459; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">Rekapitulasi Jadwal Shift Bulanan</h1>
        <p class="subtitle">Matriks Alokasi Shift Pegawai &bull; {{ \Carbon\Carbon::parse($bulan.'-01')->translatedFormat('F Y') }} &bull; Dicetak pada {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <div class="legend">
        <span class="legend-item"><strong>Keterangan Kode:</strong></span>
        <span class="legend-item"><strong>S1:</strong> Shift 1 (Pagi)</span>
        <span class="legend-item"><strong>S2:</strong> Shift 2 (Siang)</span>
        <span class="legend-item"><strong>S3:</strong> Shift 3 (Malam)</span>
        <span class="legend-item"><strong>- :</strong> Libur / Tidak ada shift</span>
    </div>

    <table class="matrix-table">
        <thead>
            <tr>
                <th class="col-pegawai" style="width: 14%;">Nama Pegawai</th>
                <th style="width: 10%;">Unit</th>
                @for ($d = 1; $d <= $daysInMonth; $d++)
                    <th style="width: 2.3%;">{{ $d }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse ($pegawais as $p)
                <tr>
                    <td class="col-pegawai" title="{{ $p->nama }}">{{ Str::limit($p->nama, 18) }}</td>
                    <td style="font-size: 7px;">{{ Str::limit(optional($p->unit)->nama_unit ?? '-', 10) }}</td>
                    @for ($d = 1; $d <= $daysInMonth; $d++)
                        @php
                            $code = $shiftMap[$p->id][$d] ?? '-';
                        @endphp
                        <td style="{{ $code !== '-' ? 'font-weight:bold; color:#1E40AF;' : 'color:#9CA3AF;' }}">
                            {{ $code }}
                        </td>
                    @endfor
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $daysInMonth + 2 }}" style="text-align: center; padding: 10px; color: #6B6459;">Data pegawai tidak ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen Resmi SIMPEG-KPI &bull; Modul Manajemen Shift Kerja
    </div>
</body>
</html>
