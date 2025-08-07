<!DOCTYPE html>
<html>
<head>
    <title>Order Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; }
        .header { text-align: center; font-weight: bold; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #222; padding: 5px; text-align: center; }
        .table th { background: #f1f1f1; }
        .total-row { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header" style="font-size: 18px;">A.Z Group</div>
    <div class="header" style="font-size: 14px;">295/JA/4/A, Rayer Bazar, Dhaka-1209</div>
    <div class="header" style="font-size: 16px;">Order Report</div>
    <div class="header" style="font-size: 13px;">
        Garment Types: {{ $order->garmentTypes->pluck('name')->join(', ') ?: 'N/A' }}
    </div>
    <div style="text-align: right; font-weight: bold;">Date: {{ $order->created_at ? $order->created_at->format('d-m-Y') : '' }}</div>

    <table class="table">
        <thead>
            <tr>
                <th>Serial No</th>
                <th>Style No</th>
                <th>Color</th>
                <th>Order Quantity</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
        @php $orderQty = 0; @endphp
        @forelse($order->color_qty as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $order->style_no ?? 'N/A' }}</td>
                <td>{{ $row['color'] ?? '' }}</td>
                <td>{{ $row['qty'] ?? 0 }}</td>
                <td>{{ $row['remarks'] ?? '' }}</td>
            </tr>
            @php $orderQty += (int) ($row['qty'] ?? 0); @endphp
        @empty
            <tr>
                <td colspan="5">No color-wise quantity data.</td>
            </tr>
        @endforelse
            <tr class="total-row">
                <td colspan="3">Total</td>
                <td>{{ $orderQty }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
