<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Production Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
        }

        .header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 0;
        }
    </style>
</head>

<body>
    <h2 class="header">A.Z Group</h2>
    <div class="header">295/JA/4/A, Rayer Bazar, Dhaka-1209</div>
    <div class="header">Daily Production Report</div>
    <div class="header">Garment Types: {{ $production->garment_type ?? 'N/A' }}</div>
    <div style="text-align:right; margin-top:8px;">Date: {{ \Carbon\Carbon::parse($production->date)->format('d-m-Y') }}
    </div>

    <table style="margin-top:16px; text-align:center;">
        <thead>
            <tr>
                <th>S.L</th>
                <th>Buyer</th>
                <th style="width: 100px;">Style No</th>
                <th>Color</th>
                <th>Factory</th>
                <th>Line</th>
                <th>Order Quantity</th>
                <th>Cutting Quantity</th>
                <th>Input</th>
                <th>Total Input</th>
                <th>Output</th>
                <th>Total Output</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @php
                $orderQty = 0;
                $cuttingQty = 0;
                $inputQty = 0;
                $totalInputQty = 0;
                $outputQty = 0;
                $totalOutputQty = 0;
                $serial = 1;
            @endphp
            @foreach ($production->production_data as $row)
                <tr>
                    <td>{{ $serial++ }}</td>
                    <td>{{ $production->order->buyer_name ?? 'N/A' }}</td>
                    <td>{{ $production->order->style_no ?? 'N/A' }}</td>
                    <td>{{ $row['color'] }}</td>
                    <td>{{ $row['factory'] }}</td>
                    <td>{{ $row['line'] }}</td>
                    <td>{{ $row['order_qty'] }}</td>
                    <td>{{ $row['cutting_qty'] }}</td>
                    <td>{{ $row['input'] }}</td>
                    <td>{{ $row['total_input'] }}</td>
                    <td>{{ $row['output'] }}</td>
                    <td>{{ $row['total_output'] }}</td>
                    <td>{{ $row['remarks'] ?? '' }}</td>
                </tr>
                @php
                    $orderQty += (int) $row['order_qty'];
                    $cuttingQty += (int) $row['cutting_qty'];
                    $inputQty += (int) $row['input'];
                    $totalInputQty += (int) $row['total_input'];
                    $outputQty += (int) $row['output'];
                    $totalOutputQty += (int) $row['total_output'];
                @endphp
            @endforeach
            <tr>
                <td colspan="6" style="font-weight:bold; text-align:center;">Total</td>
                <td style="font-weight:bold;">{{ $orderQty }}</td>
                <td style="font-weight:bold;">{{ $cuttingQty }}</td>
                <td style="font-weight:bold;">{{ $inputQty }}</td>
                <td style="font-weight:bold;">{{ $totalInputQty }}</td>
                <td style="font-weight:bold;">{{ $outputQty }}</td>
                <td style="font-weight:bold;">{{ $totalOutputQty }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</body>

</html>
