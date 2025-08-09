<div class="button-list d-flex align-items-center gap-2 fs-4 pb-1">
    {{-- for large devices --}}
    <div class="dropdown float-end d-none d-lg-block">
        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="mdi mdi-dots-vertical"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-end">
            <a href="{{ route('productions.exportPdf', $production->id) }}" class="dropdown-item"><i
                    class="mdi mdi-file-pdf-outline text-info"></i> Pdf Export</a>
            <a href="{{ route('productions.exportExcel', $production->id) }}" class="dropdown-item"><i
                    class="mdi mdi-file-excel-outline text-success"></i> Excel Export</a>
        </div>
    </div>
    {{-- For small devices --}}
    <div class="d-flex items-center gap-2 d-lg-none">
        <a href="{{ route('productions.exportPdf', $production->id) }}" class="waves-effect waves-light"><i
                class="mdi mdi-file-pdf-outline text-info"></i></a>
        <a href="{{ route('productions.exportExcel', $production->id) }}" class="waves-effect waves-light"><i
                class="mdi mdi-file-excel-outline text-success"></i></a>
    </div>
    {{-- universal --}}
    @can('view-production-report')
        <a href="{{ route('productions.show', $production->id) }}" class="waves-effect waves-light"><i
            class="mdi mdi-eye-outline text-warning"></i></a>
    @endcan
    @can('edit-production-report')
         <a href="{{ route('productions.edit', $production->id) }}" class="waves-effect waves-light"><i
            class="mdi mdi-pencil text-success"></i></a>
    @endcan
    @can('delete-production-report')
        <p type="button" class="delete-btn mb-0" data-url="{{ route('productions.destroy', $production->id) }}">
        <i class="mdi mdi-trash-can-outline text-danger"></i>
    </p>
    @endcan


</div>
