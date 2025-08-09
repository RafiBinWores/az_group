<div class="button-list d-flex align-items-center gap-2 fs-4 pb-1">
    {{-- for large devices --}}
    <div class="dropdown float-end d-none d-lg-block">
        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="mdi mdi-dots-vertical"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-end">
            <a href="{{ route('cuttings.exportPdf', $cutting->id) }}" class="dropdown-item"><i
                    class="mdi mdi-file-pdf-outline text-info"></i> Pdf Export</a>
            <a href="{{ route('cuttings.exportExcel', $cutting->id) }}" class="dropdown-item"><i
                    class="mdi mdi-file-excel-outline text-success"></i> Excel Export</a>
        </div>
    </div>
    {{-- For small devices --}}
    <div class="d-flex items-center gap-2 d-lg-none">
        <a href="{{ route('cuttings.exportPdf', $cutting->id) }}" class="waves-effect waves-light"><i
                class="mdi mdi-file-pdf-outline text-info"></i></a>
        <a href="{{ route('cuttings.exportExcel', $cutting->id) }}" class="waves-effect waves-light"><i
                class="mdi mdi-file-excel-outline text-success"></i></a>
    </div>
    {{-- universal --}}
    @can('view-cutting')
        <a href="{{ route('cuttings.show', $cutting->id) }}" class="waves-effect waves-light"><i
                class="mdi mdi-eye-outline text-warning"></i></a>
    @endcan
    @can('edit-cutting')
        <a href="{{ route('cuttings.edit', $cutting->id) }}" class="waves-effect waves-light"><i
                class="mdi mdi-pencil text-success"></i></a>
    @endcan
    @can('delete-cutting')
        <p type="button" class="delete-btn mb-0" data-url="{{ route('cuttings.destroy', $cutting->id) }}">
            <i class="mdi mdi-trash-can-outline text-danger"></i>
        </p>
    @endcan
</div>
