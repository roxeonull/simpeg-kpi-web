<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Cuti Per Unit Kerja</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #1C1712; margin: 15px; }
        .header { margin-bottom: 15px; border-b: 2px solid #C1272D; padding-bottom: 8px; }
        .title { font-size: 16px; font-weight: bold; color: #C1272D; text-transform: uppercase; margin: 0; }
        .subtitle { font-size: 11px; color: #6B6459; margin-top: 4px; }

        .unit-section { margin-bottom: 20px; }
        .unit-header { background: #F7F3EA; border: 1px solid #E7E0D2; border-left: 4px solid #C2410C; padding: 8px 12px; margin-bottom: 8px; border-radius: 4px; }
        .unit-name { font-size: 12px; font-weight: bold; color: #1C1712; margin: 0; }
        .unit-meta { font-size: 9px; color: #6B6459; margin-top: 2px; }

        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.data-table th, table.data-table td { border: 1px solid #E7E0D2; padding: 5px 7px; text-align: left; font-size: 9px; }
        table.data-table th { background: #C2410C; color: #FFFFFF; font-weight: bold; text-transform: uppercase; font-size: 8.5px; }
        table.data-table tr:nth-child(even) { background: #FAF8F5; }

        .footer { margin-top: 20px; text-align: right; font-size: 8px; color: #6B6459; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">Laporan Cuti Per Unit Kerja</h1>
        <p class="subtitle">Daftar Pengajuan Cuti Pegawai Berdasarkan Unit Kerja &bull; Tahun {{ $tahun }} &bull; Dicetak pada {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    @forelse ($unitReports as $uReport)
        <div class="unit-section">
            <div class="unit-header">
                <div class="unit-name">{{ $uReport['nama_unit'] }} {{ $uReport['kode_unit'] !== '-' ? '('.$uReport['kode_unit'].')' : '' }}</div>
                <div class="unit-meta">
                    Total Pegawai: {{ $uReport['total_pegawai'] }} orang | Total Pengajuan Cuti: <strong>{{ $uReport['total_pengajuan'] }} pengajuan</strong> | Total Hari Terpakai: <strong>{{ $uReport['total_hari'] }} hari</strong>
                </div>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 4%;">No</th>
                        <th style="width: 14%;">NIP</th>
                        <th style="width: 22%;">Nama Pegawai</th>
                        <th style="width: 18%;">Jenis Cuti</th>
                        <th style="width: 12%;">Tgl Mulai</th>
                        <th style="width: 12%;">Tgl Selesai</th>
                        <th style="width: 8%;">Hari</th>
                        <th style="width: 10%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($uReport['cutis'] as $index => $c)
                        <tr>
                            <td style="text-align: center;">{{ $index + 1 }}</td>
                            <td>{{ optional($c->pegawai)->nip ?? '-' }}</td>
                            <td><strong>{{ optional($c->pegawai)->nama ?? '-' }}</strong></td>
                            <td>{{ optional($c->jenisCuti)->nama ?? ucfirst($c->jenis_cuti) }}</td>
                            <td style="text-align: center;">{{ $c->tanggal_mulai ? $c->tanggal_mulai->format('d/m/Y') : '-' }}</td>
                            <td style="text-align: center;">{{ $c->tanggal_selesai ? $c->tanggal_selesai->format('d/m/Y') : '-' }}</td>
                            <td style="text-align: center; font-weight: bold;">{{ $c->jumlah_hari }} hari</td>
                            <td style="text-align: center;">{{ $c->statusLabel() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 10px; color: #6B6459;">Tidak ada pengajuan cuti pada unit kerja ini di tahun {{ $tahun }}.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @empty
        <p style="text-align: center; padding: 20px; color: #6B6459;">Unit kerja tidak ditemukan.</p>
    @endforelse

    <div class="footer">
        Dokumen Resmi SIMPEG-KPI &bull; Modul Rekapitulasi Cuti Per Unit Kerja
    </div>
</body>
</html>
