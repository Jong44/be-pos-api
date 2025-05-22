<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Kasir</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; margin: 20px; }
        h2, h4 { margin: 0; padding: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: left; }
        .no-border { border: none !important; }
        .summary { margin-top: 30px; }
    </style>
</head>
<body>

    <h2>Laporan Kasir</h2>
    <p>{{ $header['outlet_name'] }}</p>
    <p>Period: {{ $header['start_date'] }} - {{ $header['end_date'] }}</p>

    @foreach($reports as $report)
        <hr>
        <p><strong>Kasir :</strong> {{ $report['user'] }} # {{ $report['number'] }}
        <span style="float:right;"><strong>Tanggal :</strong> {{ $report['created_at'] }}</span></p>

        <table>
            <thead>
                <tr>
                    <th>Item-item</th>
                    <th>Harga</th>
                    <th>Biaya</th>
                    <th>Diskon</th>
                    <th>Total Harga</th>
                    <th>Total Cost</th>
                    <th>Total Setelah Discount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report['transaction']['items'] as $item)
                    <tr>
                        <td>{{ $item['product'] }} {{ $item['product_price'] }} x {{ $item['quantity'] }}</td>
                        <td>{{ $item['product_price'] }}</td>
                        <td>{{ $item['product_cost'] }}</td>
                        <td>{{ $item['discount_price'] }}</td>
                        <td>{{ $item['price'] }}</td>
                        <td>{{ $item['cost'] }}</td>
                        <td>{{ $item['total_after_discount'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="no-border">
            <tr><td class="no-border"><strong>Sub Total</strong></td><td class="no-border">( {{ $report['total']['discount'] }} )</td></tr>
            <tr><td class="no-border"><strong>Gross Selling:</strong></td><td class="no-border">{{ $report['total']['gross_selling'] }}</td></tr>
            <tr><td class="no-border"><strong>Total Cost:</strong></td><td class="no-border">{{ $report['total']['cost'] }}</td></tr>
            <tr><td class="no-border"><strong>Total Setelah Discount:</strong></td><td class="no-border">{{ $report['total']['grand_total'] }}</td></tr>
        </table>
    @endforeach

    <hr>
    <div class="summary">
        <h4>Grand Total</h4>
        <table>
            <tr>
                <td>Biaya Penjualan</td>
                <td>{{ $footer['total_cost'] }}</td>
            </tr>
            <tr>
                <td>Discount per Penjualan</td>
                <td>{{ $footer['total_discount'] }}</td>
            </tr>
            <tr>
                <td>Penjualan Setelah Discount</td>
                <td>{{ $footer['total_net'] }}</td>
            </tr>
            <tr>
                <td>Keuntungan Kotor</td>
                <td>{{ $footer['total_gross_profit'] }}</td>
            </tr>
            <tr>
                <td>Keuntungan Bersih Sebelum Diskon Penjualan</td>
                <td>{{ $footer['total_net_profit_before_discount_selling'] }}</td>
            </tr>
            <tr>
                <td>Keuntungan Bersih Setelah Diskon Penjualan</td>
                <td>{{ $footer['total_net_profit_after_discount_selling'] }}</td>
            </tr>
        </table>
    </div>

</body>
</html>
