<div class="button-list d-flex align-items-center gap-2 fs-4 pb-1">
    {{-- <button type="button" class="btn btn-success waves-effect waves-light"><i
            class="mdi mdi-heart-half-full"></i></button>
    <button type="button" class="btn btn-danger waves-effect waves-light"><i class="mdi mdi-close"></i></button>
    <button type="button" class="btn btn-info waves-effect waves-light"><i class="mdi mdi-music"></i></button>
    <button type="button" class="btn btn-warning waves-effect waves-light"><i class="mdi mdi-star"></i></button> --}}
    <a href="{{ route('garment_types.edit', $garment->id) }}" class="waves-effect waves-light"><i
            class="mdi mdi-pencil text-success"></i></a>
    <p type="button"
        class="delete-btn mb-0"
        data-url="{{ route('garment_types.destroy', $garment->id) }}">
    <i class="mdi mdi-trash-can-outline text-danger"></i>
</p>

</div>
