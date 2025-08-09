<div class="button-list d-flex align-items-center gap-2 fs-4 pb-1">

    @can('edit-garments')
        <a href="{{ route('garment_types.edit', $garment->id) }}" class="waves-effect waves-light"><i
            class="mdi mdi-pencil text-success"></i></a>
    @endcan
    @can('delete-garments')
        <p type="button" class="delete-btn mb-0" data-url="{{ route('garment_types.destroy', $garment->id) }}">
        <i class="mdi mdi-trash-can-outline text-danger"></i>
    </p>
    @endcan

</div>
