<x-layout title="Profile">
    <div class="container mt-5">
        <h2 data-translate>User Profile</h2>

        <div class="row mt-4">
            <div class="col-md-4 text-center">
                @if($user->profile_image)
                    <img src="{{ $user->profile_image }}" alt="Profile Image" class="img-fluid rounded-circle mb-3" style="max-width: 200px; height: 200px; object-fit: cover;">
                @else
                    <img src="https://i.pinimg.com/474x/47/ba/71/47ba71f457434319819ac4a7cbd9988e.jpg" alt="No Profile Image" class="img-fluid rounded-circle mb-3">
                @endif
            </div>

            <div class="col-md-8">
                <h3>{{ $user->name }}</h3>
                <p><strong data-translate>Email:</strong> {{ $user->email }}</p>
                <p><strong data-translate>Location:</strong> {{ $user->location ?? 'Not set' }}</p>
                <p><strong data-translate>About Me:</strong><br> {{ $user->profile_description ?? 'No description provided.' }}</p>
                <p><strong data-translate>Average Rating:</strong> {{ number_format($user->reviewsReceived()->avg('rating'), 2) }}</p>
                <p><strong data-translate>Items Listed:</strong> {{ $user->items->count() }}</p>

                @if(auth()->id() !== $user->id)
                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-outline-primary" onclick="startChat({{ $user->id }}, '{{ $user->name }}')">
                            Chat with {{ $user->name }}
                        </button>

                        @if(auth()->user()->isAdmin())
                            <form action="{{ route('admin.users.delete', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete {{ $user->name }}?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger" data-translate>Delete User</button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>

            <h4 data-translate>Received Reviews:</h4>
            @foreach ($reviews as $review)
                <div>
                    <strong>{{ $review->reviewer->name }}</strong> <span data-translate>rated</span> {{ $review->rating }}/5
                    <p>{{ $review->comment }}</p>
                </div>
            @endforeach

            <h3>{{ $user->name }}’s Listings:</h3>
            <div class="row">
                @foreach ($items as $item)
                    <div class="col-md-3">
                        <x-item-card :item="$item" :isSold="$item->status === 'sold'" />
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layout>
