<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Dokumen Aset - {{ $asset->asset_code }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #4A148C;
            padding-bottom: 8px;
        }

        .header h2 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
            color: #4A148C;
        }

        .header p {
            margin: 4px 0 0 0;
            font-size: 11px;
            color: #555;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #4A148C;
            margin-top: 15px;
            margin-bottom: 5px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
        }

        .table-profile {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .table-profile td {
            padding: 6px;
            vertical-align: top;
            border-bottom: 1px solid #f0f0f0;
        }

        .table-profile td.label {
            width: 30%;
            font-weight: bold;
            color: #555;
            background-color: #fcf9fe;
        }

        .table-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 11px;
        }

        .table-grid th {
            background-color: #4A148C;
            color: #ffffff;
            padding: 6px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .table-grid td {
            padding: 6px;
            border: 1px solid #ddd;
        }

        .badge-critical {
            background-color: #d32f2f;
            color: white;
            padding: 2px 5px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 10px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer-sign {
            margin-top: 30px;
            float: right;
            text-align: center;
            width: 200px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>PT Kaltim Methanol Industri</h2>
        <p>Lembar Profil Ringkasan Siklus Hidup & Monitoring Alat Kerja IT</p>
    </div>

    <table style="width: 100%; margin-bottom: 10px;">
        <tr>
            <td><strong>Nomor Dokumen:</strong> INF/ASD/{{ $asset->id }}/{{ now()->format('Y') }}</td>
            <td style="text-align: right;"><strong>Tanggal Unduh:</strong> {{ now()->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    <div class="section-title">A. Profil dan Spesifikasi Aset</div>
    <table class="table-profile">
        <tr>
            <td class="label">Kode Inventaris Aset</td>
            <td><strong>{{ $asset->asset_code }}</strong></td>
        </tr>
        <tr>
            <td class="label">Nama Perangkat</td>
            <td>{{ $asset->name }}</td>
        </tr>
        <tr>
            <td class="label">Kategori</td>
            <td>{{ $asset->category ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Merek & Tipe</td>
            <td>{{ $asset->brand ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Nomor Seri / Lisensi</td>
            <td><code>{{ $asset->serial_number ?? '-' }}</code></td>
        </tr>
    </table>

    <div class="section-title">B. Penempatan & Siklus Hidup (EOL)</div>
    <table class="table-profile">
        <tr>
            <td class="label">Departemen Pengguna</td>
            <td>{{ $asset->department ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Pembelian</td>
            <td>{{ $asset->purchase_date ? $asset->purchase_date->translatedFormat('d F Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Jadwal Tanggal End of Life (EOL)</td>
            <td style="{{ $asset->eol_date && $asset->eol_date->isPast() ? 'color: red; font-weight: bold;' : 'color: green;' }}">
                {{ $asset->eol_date ? $asset->eol_date->translatedFormat('d F Y') : '-' }}
                @if($asset->eol_date && $asset->eol_date->isPast()) (Sudah Melewati Batas Waktu) @endif
            </td>
        </tr>
        <tr>
            <td class="label">Status Saat Ini</td>
            <td><strong>{{ $asset->status ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="label">Catatan Tambahan</td>
            <td>{{ $asset->description ?? '-' }}</td>
        </tr>
    </table>

    <div class="section-title">D. Log Pemeliharaan & Perbaikan Berkala (*Maintenance*)</div>
    @if($asset->maintenanceLogs && $asset->maintenanceLogs->count() > 0)
    <table class="table-grid">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="15%">Tanggal Kerja</th>
                <th width="15%">Jenis Perawatan</th>
                <th width="35%">Deskripsi Tindakan Teknisi</th>
                <th width="15%" class="text-right">Biaya (Rp)</th>
                <th width="15%">Pelaksana</th>
            </tr>
        </thead>
        <tbody>
            @foreach($asset->maintenanceLogs as $idx => $log)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ $log->maintenance_date ? $log->maintenance_date->format('d-m-Y') : '-' }}</td>
                <td>{{ $log->maintenance_type }}</td>
                <td>{{ $log->description }}</td>
                <td class="text-right">{{ number_format($log->cost, 2, ',', '.') }}</td>
                <td>{{ $log->performed_by }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="color: #777; font-style: italic; margin-top: 5px;">Belum ada catatan aktivitas pemeliharaan teknis yang terekam pada perangkat ini.</p>
    @endif

    <div class="footer-sign">
        <p>Bontang, {{ now()->translatedFormat('d F Y') }}</p>
        <br><br><br>
        <p><strong>Tim Administrasi Aset IT</strong><br>PT Kaltim Methanol Industri</p>
    </div>

</body>

</html>