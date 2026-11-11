<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Petugas</title>
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
    <h1>Laporan Data Petugas</h1>
    <p>Dicetak pada {{ now()->format('d-m-Y H:i') }} · Total {{ $petugas->count() }} petugas</p>
    <table>
        <thead>
            <tr>
                <th>Id Petugas</th>
                <th>Username</th>
                <th>Nomor Telepon</th>
                <th>Nama Petugas</th>
                <th>Jabatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($petugas as $item)
                <tr>
                    <td>{{ $item->user_id }}</td>
                    <td>{{ $item->username }}</td>
                    <td>{{ $item->phone_number ?: '—' }}</td>
                    <td>{{ $item->full_name ?: '—' }}</td>
                    <td>{{ $item->position ?: '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Data petugas tidak ditemukan.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
