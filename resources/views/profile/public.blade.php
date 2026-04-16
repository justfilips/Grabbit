<x-layout title="Profile">
<div class="container mt-5">

    <h2>User Profile</h2>
    <div class="row mt-4">
        <div class="col-md-4 text-center">
            @if($user->profile_image)
                <img src="{{ $user->profile_image }}"
                     class="img-fluid rounded-circle mb-3"
                     style="max-width:200px; height:200px; object-fit:cover;">
            @else
                <img src="https://i.pinimg.com/474x/47/ba/71/47ba71f457434319819ac4a7cbd9988e.jpg"
                     class="img-fluid rounded-circle mb-3"
                     style="max-width:200px;">
            @endif
        </div>
        <div class="col-md-8">

            <h3>{{ $user->name }}</h3>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Location:</strong> {{ $user->location ?? 'Not set' }}</p>
            <p><strong>About Me:</strong><br>
                {{ $user->profile_description ?? 'No description provided.' }}
            </p>
            <p><strong>Average Rating:</strong>
                {{ number_format($user->reviewsReceived()->avg('rating') ?? 0, 2) }}
            </p>
            <p><strong>Items Listed:</strong>
                {{ $user->items->count() }}
            </p>

            @auth
                @if(auth()->id() !== $user->id)
                    <button class="btn btn-outline-primary"
                            onclick="startChat({{ $user->id }}, '{{ $user->name }}')">
                        Chat with {{ $user->name }}
                    </button>
                @endif
            @endauth

            @auth
                @if(auth()->user()->isAdmin())
                    <form action="{{ route('admin.users.delete', $user) }}"
                          method="POST"
                          class="mt-2"
                          onsubmit="return confirm('Are you sure you want to delete {{ $user->name }}?');">

                        @csrf
                        @method('DELETE')

                        <button class="btn btn-outline-danger">
                            Delete User
                        </button>
                    </form>
                @endif
            @endauth

        </div>
    </div>

    <h4 class="mt-4">Received Reviews:</h4>

    @foreach ($reviews as $review)
        <div class="border rounded p-2 mb-2">
            <strong>{{ $review->reviewer->name }}</strong>
            <span>rated {{ $review->rating }}/5</span>
            <p class="mb-0">{{ $review->comment }}</p>
        </div>
    @endforeach

    <h3 class="mt-4">{{ $user->name }}’s Listings:</h3>

    <div class="row">
        @foreach ($items as $item)
            <div class="col-md-3">
                <x-item-card :item="$item" :isSold="$item->status === 'sold'" />
            </div>
        @endforeach
    </div>

</div>
</x-layout>