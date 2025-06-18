<x-layout title="My Profile">
    <div class="container mt-5">
        <h2 data-translate>My Profile</h2>

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

                <a href="{{ route('profile.edit') }}" class="btn btn-primary mt-3" data-translate>Edit Profile</a>
            </div>
        </div>

        <h4 data-translate>Received Reviews:</h4>
        @foreach ($reviews as $review)
            <div>
                <strong>{{ $review->reviewer->name }}</strong> <span data-translate>rated</span> {{ $review->rating }}/5
                <p>{{ $review->comment }}</p>
            </div>
        @endforeach

        <h3 data-translate>My Listings</h3>
        <div class="row">
            @foreach ($myListings as $item)
                <div class="col-md-4 mb-3">
                    <x-item-card :item="$item" :isSold="$item->status === 'sold'" />
                </div>
            @endforeach
        </div>

        <h3 data-translate>Purchased Listings</h3>
        <div class="row">
            @forelse ($boughtItems as $item)
                <div class="col-md-4">
                    <x-item-card :item="$item" />
                    @if(!$item->reviews()->where('reviewer_id', Auth::id())->exists())
                        <form action="{{ route('reviews.create') }}" method="GET">
                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                            <input type="hidden" name="reviewed_id" value="{{ $item->user_id }}">
                            <button type="submit" class="btn btn-sm btn-outline-primary" data-translate>Review Seller</button>
                        </form>
                    @endif
                </div>
            @empty
                <p>No purchased listings.</p>
            @endforelse
        </div>
    </div>
</x-layout>
