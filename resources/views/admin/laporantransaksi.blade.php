<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Transaksi</title>
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
    <h1>Laporan Data Transaksi</h1>
    <p>Dicetak pada {{ now()->format('d-m-Y H:i') }} · Total {{ $transactions->count() }} transaksi</p>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Nominal</th>
                <th>Status Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $transaction)
                @php($transactionDate = \Illuminate\Support\Carbon::parse($transaction->transaction_date))
                <tr>
                    <td>#{{ $transaction->transaction_id }}</td>
                    <td>{{ $transaction->username }}</td>
                    <td>{{ $transactionDate->format('d-m-Y') }}</td>
                    <td>{{ $transactionDate->format('H:i') }}</td>
                    <td>Rp {{ number_format((float) $transaction->amount, 0, ',', '.') }}</td>
                    <td>{{ $transaction->transaction_status === 'Selesai' ? 'Lunas' : $transaction->transaction_status }}</td>
                </tr>
            @empty
                <tr><td colspan="6">Data transaksi tidak ditemukan.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
