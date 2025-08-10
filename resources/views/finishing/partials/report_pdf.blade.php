<!DOCTYPE html>
<html>
<head>
    <title>Finishing Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; }
        .header { text-align: center; font-weight: bold; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th, .table td { border: 1px solid #222; padding: 5px; text-align: center; }
        .table th { background: #f1f1f1; }
        .meta-table { width: 100%; margin-top: 15px; }
        .meta-table td { padding: 3px 5px; }
        .label { font-weight: bold; width: 180px; }
    </style>
</head>
<body>
    {{-- Company Info --}}
    <div class="header" style="font-size: 18px;">A.Z Group</div>
    <div class="header" style="font-size: 14px;">295/JA/4/A, Rayer Bazar, Dhaka-1209</div>
    <div class="header" style="font-size: 16px;">Finishing Report</div>

    {{-- Garment Types & Date --}}
    <div class="header" style="font-size: 13px;">
        Garment Types: {{ $finishing->order?->garmentTypes->pluck('name')->join(', ') ?: 'N/A' }}
    </div>
    <div style="text-align: right; font-weight: bold;">
        Date: {{ $finishing->date ? $finishing->date : '' }}
    </div>

    {{-- Meta Info --}}
    <table class="meta-table">
        <tr>
            <td class="label">Buyer Name:</td>
            <td>{{ $finishing->order?->buyer_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Style No:</td>
            <td>{{ $finishing->order?->style_no ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Order Quantity:</td>
            <td>{{ $finishing->order?->order_qty ?? 'N/A' }}</td>
        </tr>
    </table>

    {{-- Finishing Details --}}
    <table class="table">
        <thead>
            <tr>
                <th>Thread Cutting</th>
                <th>QC Check</th>
                <th>Button & Rivet Attach</th>
                <th>Iron</th>
                <th>Hangtag</th>
                <th>Poly</th>
                <th>Carton</th>
                <th>Today Finishing</th>
                <th>Total Finishing</th>
                <th>Plan To Complete</th>
                <th>DPI Inline</th>
                <th>FRI Final</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $finishing->thread_cutting ?? 'N/A' }}</td>
                <td>{{ $finishing->qc_check ?? 'N/A' }}</td>
                <td>{{ $finishing->button_rivet_attach ?? 'N/A' }}</td>
                <td>{{ $finishing->iron ?? 'N/A' }}</td>
                <td>{{ $finishing->hangtag ?? 'N/A' }}</td>
                <td>{{ $finishing->poly ?? 'N/A' }}</td>
                <td>{{ $finishing->carton ?? 'N/A' }}</td>
                <td>{{ $finishing->today_finishing ?? 'N/A' }}</td>
                <td>{{ $finishing->total_finishing ?? 'N/A' }}</td>
                <td>{{ $finishing->plan_to_complete ?? 'N/A' }}</td>
                <td>{{ $finishing->dpi_inline ?? 'N/A' }}</td>
                <td>{{ $finishing->fri_final ?? 'N/A' }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
