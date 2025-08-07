@props([
    'id' => 'userOffcanvas',
    'title' => 'Add User',
    'action' => route('users.store'),
    'method' => 'POST', // or 'PUT'
    'roles' => [],
    'user' => null,
])

<div class="offcanvas offcanvas-end" tabindex="-1" id="{{ $id }}" aria-labelledby="{{ $id }}Label">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="{{ $id }}Label">{{ $title }}</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <form id="{{ $id }}Form" action="{{ $action }}" method="POST" enctype="multipart/form-data"
            class="needs-validation" novalidate>
            @csrf
            @if ($method === 'PUT')
                @method('PUT')
            @endif
            <!-- Name -->
            <div class="mb-3">
                <label class="form-label">User Name</label>
                <input type="text" class="form-control" name="name" id="{{ $id }}-name" placeholder="Enter user name"
                    value="{{ $user->name ?? '' }}" required>
                <div class="error"></div>
            </div>
            <!-- Email -->
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control" name="email" id="{{ $id }}-email" placeholder="Enter user email"
                    value="{{ $user->email ?? '' }}" required>
                <div class="error"></div>
            </div>
            <!-- Role -->
            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-select" id="{{ $id }}-role" required>
                    <option value="">Select a role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}" @if (isset($user) && $user->role === $role->name) selected @endif>
                            {{ $role->name }}</option>
                    @endforeach
                </select>
                <div class="error"></div>

            </div>
            <!-- Password (Add only or allow change) -->
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" id="{{ $id }}-password"
                    autocomplete="new-password"
                    placeholder="Enter your password" required>
                <div class="error"></div>
            </div>
            <!-- Avatar -->
            <div class="mb-3">
                <label class="form-label">Profile Picture</label>
                <input type="file" class="form-control" name="avatar" id="{{ $id }}-avatar">
                @if (isset($user) && $user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="rounded mt-2"
                        style="width:60px;">
                @endif
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                <button type="submit" class="btn btn-success">{{ $user ? 'Update User' : 'Create User' }}</button>
            </div>
        </form>
    </div>
</div>
