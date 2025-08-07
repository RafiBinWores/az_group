<div class="button-list d-flex align-items-center gap-2 fs-4 pb-1">
    <a href="{{ route('factories.edit', $factory->id) }}" class="waves-effect waves-light"><i
            class="mdi mdi-pencil text-success"></i></a>
    <p type="button"
        class="delete-btn mb-0"
        data-url="{{ route('factories.destroy', $factory->id) }}">
    <i class="mdi mdi-trash-can-outline text-danger"></i>
</p>

</div>
