<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cutting Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #f2f2f2; }
        .header { text-align: center; font-weight: bold; margin-bottom: 0; }
    </style>
</head>
<body>
    <h2 class="header">A.Z Group</h2>
    <div class="header">295/JA/4/A, Rayer Bazar, Dhaka-1209</div>
    <div class="header">Daily Cutting Report</div>
    <div class="header">Garment Types: {{ $cutting->garment_type ?? 'N/A' }}</div>
    <div style="text-align:right; margin-top:8px;">Date: {{ \Carbon\Carbon::parse($cutting->date)->format('d-m-Y') }}</div>

    <table style="margin-top:16px;">
        <thead>
            <tr>
                <th>S.L</th>
                <th>Style No</th>
                <th>Color</th>
                <th>Order Quantity</th>
                <th>Cutting Quantity</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @php
                $orderQty = 0; $cuttingQty = 0; $serial = 1;
            @endphp
            @foreach($cutting->cutting as $row)
                <tr>
                    <td>{{ $serial++ }}</td>
                    <td>{{ $cutting->order->style_no ?? 'N/A' }}</td>
                    <td>{{ $row['color'] }}</td>
                    <td>{{ $row['order_qty'] }}</td>
                    <td>{{ $row['cutting_qty'] }}</td>
                    <td>{{ $row['remarks'] ?? '' }}</td>
                </tr>
                @php
                    $orderQty += (int) $row['order_qty'];
                    $cuttingQty += (int) $row['cutting_qty'];
                @endphp
            @endforeach
            <tr>
                <td colspan="3" style="font-weight:bold; text-align:center;">Total</td>
                <td style="font-weight:bold;">{{ $orderQty }}</td>
                <td style="font-weight:bold;">{{ $cuttingQty }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
