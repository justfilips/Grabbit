<x-layout title="My Wishlist">
    <div class="container mt-4">
        <h2>Your Wishlist</h2>
        <div class="row">
            @forelse ($wishlistItems as $item)
                <div class="col-md-4 mb-3">
                    <div class="card">
                        @if($item->images->isNotEmpty())
                            <img src="{{ $item->images->first()->image_path }}" class="card-img-top" alt="{{ $item->title }}">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $item->title }}</h5>
                            <p>€{{ number_format($item->price, 2) }}</p>
                            <a href="{{ route('items.show', $item->id) }}" class="btn btn-primary">View</a>
                        </div>
                    </div>
                </div>
            @empty
                <p>You haven't added anything yet!</p>
            @endforelse
        </div>
    </div>
</x-layout>
