<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Laporan Penjualan</h2>
    <p>{{ $header['outlet_name'] }}</p>
    <p>Period: {{ $header['start_date'] }} - {{ $header['end_date'] }}</p>

    <table>
        <thead>
            <tr>
                <th>Metode Pembayaran</th>
                <th>Product Name</th>
                <th>Harga</th>
                <th>Kuantitas</th>
                <th>Penjualan</th>
                <th>Net Selling</th>
                <th>Gross Profit</th>
                <th>Net Profit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $transaction)
                @foreach ($transaction->transactionDetails as $item)
                <tr>
                    <td>{{ $transaction->paymentMethod->name ?? '-' }}</td>
                    <td>{{ $item->product->name ?? '-' }}</td>
                    <td>{{ $item->price / $item->qty }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $item->price }}</td>
                    <td>{{ $item->price }}</td>
                    <td>{{ $item->price - $item->cost }}</td>
                    <td>{{ $item->price - $item->cost }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <h3>Summary</h3>
    <table>
        <thead>
            <tr>
                <th>Biaya</th>
                <th>Total Kuantitas</th>
                <th>Total Penjualan</th>
                <th>Diskon per Penjualan</th>
                <th>Penjualan Setelah Discount</th>
                <th>Keuntungan Kotor</th>
                <th>Keuntungan Bersih sebelum diskon</th>
                <th>Keuntungan Bersih Setelah Diskon</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $footer['total_cost'] }}</td>
                <td>{{ $footer['total_qty'] }}</td>
                <td>{{ $footer['total_gross'] }}</td>
                <td>{{ $footer['total_discount'] }}</td>
                <td>{{ $footer['total_net'] }}</td>
                <td>{{ $footer['total_gross_profit'] }}</td>
                <td>{{ $footer['total_net_profit_before_discount_selling'] }}</td>
                <td>{{ $footer['total_net_profit_after_discount_selling'] }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
