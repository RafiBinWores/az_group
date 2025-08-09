<div class="button-list d-flex align-items-center gap-2 fs-4 pb-1">
    @can('edit-lines')
        <a href="{{ route('lines.edit', $line->id) }}" class="waves-effect waves-light"><i
            class="mdi mdi-pencil text-success"></i></a>
    @endcan
    @can('delete-lines')
        <p type="button" class="delete-btn mb-0" data-url="{{ route('lines.destroy', $line->id) }}">
        <i class="mdi mdi-trash-can-outline text-danger"></i>
    </p>
    @endcan
</div>
