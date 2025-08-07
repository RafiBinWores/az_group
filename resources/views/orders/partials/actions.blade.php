<div class="button-list d-flex align-items-center gap-2 fs-4 pb-1">
    {{-- for large devices --}}
    <div class="dropdown float-end d-none d-lg-block">
        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="mdi mdi-dots-vertical"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-end">
            <a href="{{ route('orders.exportPdf', $order->id) }}" class="dropdown-item"><i
                    class="mdi mdi-file-pdf-outline text-info"></i> Pdf Export</a>
            <a href="{{ route('orders.export', $order->id) }}" class="dropdown-item"><i
                    class="mdi mdi-file-excel-outline text-success"></i> Excel Export</a>
        </div>
    </div>
    {{-- For small devices --}}
    <div class="d-flex items-center gap-2 d-lg-none">
        <a href="{{ route('orders.exportPdf', $order->id) }}" class="waves-effect waves-light"><i
                class="mdi mdi-file-pdf-outline text-info"></i></a>
        <a href="{{ route('orders.export', $order->id) }}" class="waves-effect waves-light"><i
                class="mdi mdi-file-excel-outline text-success"></i></a>
    </div>
    {{-- universal --}}
    <a href="{{ route('orders.show', $order->id) }}" class="waves-effect waves-light"><i
            class="mdi mdi-eye-outline text-warning"></i></a>
    <a href="{{ route('orders.edit', $order->id) }}" class="waves-effect waves-light"><i
            class="mdi mdi-pencil text-success"></i></a>
    <p type="button" class="delete-btn mb-0" data-url="{{ route('orders.destroy', $order->id) }}">
        <i class="mdi mdi-trash-can-outline text-danger"></i>
    </p>

</div>
