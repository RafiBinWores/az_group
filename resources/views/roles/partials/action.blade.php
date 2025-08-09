<div class="button-list">
    {{-- <button type="button" class="btn btn-success waves-effect waves-light"><i
            class="mdi mdi-heart-half-full"></i></button>
    <button type="button" class="btn btn-danger waves-effect waves-light"><i class="mdi mdi-close"></i></button>
    <button type="button" class="btn btn-info waves-effect waves-light"><i class="mdi mdi-music"></i></button>
    <button type="button" class="btn btn-warning waves-effect waves-light"><i class="mdi mdi-star"></i></button> --}}
    @can('edit-roles')
        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-primary waves-effect waves-light"><i
            class="mdi mdi-pencil"></i></a>
    @endcan
    @can('delete-roles')
        <button type="button" class="btn btn-danger delete-btn" data-url="{{ route('roles.destroy', $role->id) }}">
        <i class="mdi mdi-trash-can-outline"></i>
    </button>
    @endcan

</div>
