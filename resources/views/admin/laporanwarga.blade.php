<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Warga</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #26352d; font-size: 10px; }
        h1 { margin: 0 0 5px; color: #246c4d; font-size: 18px; }
        p { margin: 0 0 16px; color: #6b7770; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid #d6e0da; text-align: left; }
        th { background: #2F8961; color: #fff; }
        tr:nth-child(even) td { background: #f2f5f3; }
    </style>
</head>
<body>
    <h1>Laporan Data Warga</h1>
    <p>Dicetak pada {{ now()->format('d-m-Y H:i') }} · Total {{ $wargas->count() }} warga</p>
    <table>
        <thead>
            <tr>
                <th>Nama Warga</th>
                <th>Username</th>
                <th>Nomor Telepon</th>
                <th>Alamat Jalan</th>
                <th>Blok Rumah</th>
                <th>RT</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($wargas as $warga)
                <tr>
                    <td>{{ $warga->full_name ?: '—' }}</td>
                    <td>{{ $warga->username }}</td>
                    <td>{{ $warga->phone_number ?: '—' }}</td>
                    <td>{{ $warga->address ?: '—' }}</td>
                    <td>{{ $warga->blok_rumah ?: '—' }}</td>
                    <td>{{ $warga->rt ?: '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="6">Data warga tidak ditemukan.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
