<x-layouts.app>

    <x-slot name="title">Notifications</x-slot>
    <x-slot name="pageTitle">Notifications</x-slot>


    <div class="container ">
        @if (auth()->user()->unreadNotifications->count())
            <form method="POST" action="{{ route('notifications.readAll') }}">
                @csrf
                <button type="submit" class="btn btn-success text-dark mb-2" style="font-size:12px;">
                    <small>Mark all as read!</small>
                </button>
            </form>
        @endif
        <ul class="list-group">
            @forelse($notifications as $notification)
                @php
                    $data = $notification->data;
                    if (isset($data['cutting_id'])) {
                        $url = route('notifications.read', $notification->id);
                    } elseif (isset($data['embroidery_id'])) {
                        $url = route('notifications.read', $notification->id);
                    } elseif (isset($data['print_id'])) {
                        $url = route('notifications.read', $notification->id);
                    } elseif (isset($data['wash_id'])) {
                        $url = route('notifications.read', $notification->id);
                    } elseif (isset($data['production_id'])) {
                        $url = route('notifications.read', $notification->id);
                    } else {
                        $url = '#';
                    }
                @endphp

                @if (!$notification->read_at)
                    <form method="POST" action="{{ $url }}" class="w-100">
                        @csrf
                        <button type="submit"
                            class="list-group-item d-flex border-0 bg-transparent w-100 justify-content-between align-items-center fw-bold bg-light"
                            style="text-align:left; cursor:pointer;">
                            <div>
                                {{ $notification->data['message'] ?? 'Notification' }}
                                <br>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                        </button>
                    </form>
                @else
                    <a href="@php
                        if(isset($data['cutting_id'])) {
                            echo route('cuttings.show', $data['cutting_id']);
                        } elseif(isset($data['embroidery_id'])) {
                            echo route('embroideries.show', $data['embroidery_id']);
                        } elseif(isset($data['print_id'])) {
                            echo route('prints.show', $data['print_id']);
                        } elseif(isset($data['wash_id'])) {
                            echo route('washes.show', $data['wash_id']);
                        } elseif(isset($data['production_id'])) {
                            echo route('productions.show', $data['production_id']);
                        } else {
                            echo '#';
                        } @endphp"
                        class="list-group-item d-flex justify-content-between align-items-center"
                        style="text-align:left;">
                        <div>
                            {{ $notification->data['message'] ?? 'Notification' }}
                            <br>
                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                    </a>
                @endif
            @empty
                <li class="list-group-item text-muted">No notifications found.</li>
            @endforelse
        </ul>
        <div class="mt-3">
            {{ $notifications->links() }}
        </div>
    </div>

</x-layouts.app>
