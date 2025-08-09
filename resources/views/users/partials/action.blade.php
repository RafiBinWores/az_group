<div class="button-list d-flex align-items-center gap-2 fs-4">
    @can('edit-users')
        <a href="{{ route('users.edit', $user->id) }}" class="waves-effect waves-light"><i
                class="mdi mdi-pencil text-success"></i></a>
    @endcan

    @can('delete-users')
        <p type="button" class="delete-btn mb-0" data-url="{{ route('users.destroy', $user->id) }}">
            <i class="mdi mdi-trash-can-outline text-danger"></i>
        </p>
    @endcan
    </button>

</div>
