<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kunjungan</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Laporan Kunjungan</h1>
    <table>
        <thead>
            <tr>
                <th>Kota</th>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>Asal Kebangsaan</th>
                <th>Jumlah Pengunjung</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $report)
                <tr>
                    <td>{{ $report->nama_kota ?? 'N/A' }}</td>
                    <td>{{ $report->bulan }}</td>
                    <td>{{ $report->tahun }}</td>
                    <td>{{ $report->nama_negara ?? 'N/A' }}</td>
                    <td>{{ number_format($report->jumlah_kunjungan, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
