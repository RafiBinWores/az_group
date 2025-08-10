<div class="button-list d-flex align-items-center gap-2 fs-4 pb-1">
    <div class="d-flex items-center gap-2">
        <a href="{{ route('finishing.exportPdf', $finishing->id) }}" class="waves-effect waves-light"><i
                class="mdi mdi-file-pdf-outline text-info"></i></a>
        <a href="{{ route('finishing.exportExcel', $finishing->id) }}" class="waves-effect waves-light"><i
                class="mdi mdi-file-excel-outline text-success"></i></a>
    </div>
    {{-- universal --}}
    @can('view-finishing-report')
        <a href="{{ route('finishing.show', $finishing->id) }}" class="waves-effect waves-light"><i
                class="mdi mdi-eye-outline text-warning"></i></a>
    @endcan
    @can('edit-finishing-report')
        <a href="{{ route('finishing.edit', $finishing->id) }}" class="waves-effect waves-light"><i
                class="mdi mdi-pencil text-success"></i></a>
    @endcan
    @can('delete-finishing-report')
        <p type="button" class="delete-btn mb-0" data-url="{{ route('finishing.destroy', $finishing->id) }}">
            <i class="mdi mdi-trash-can-outline text-danger"></i>
        </p>
    @endcan

</div>
