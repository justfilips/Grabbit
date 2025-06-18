<x-layout title="My Wishlist">
    <div class="container mt-4">
        <h2 data-translate>Your Wishlist</h2>
        <div class="row">
            @forelse ($wishlistItems as $item)
                <div class="col-md-4 mb-3">
                    <div class="card flex-column">
                        @if($item->images->isNotEmpty())
                            <img src="{{ $item->images->first()->image_path }}" class="card-img-top" alt="{{ $item->title }}">
                        @endif
                        @auth
                            @if ($item->user_id !== auth()->id())
                                <form action="{{ route('wishlist.toggle', $item->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-link p-3">
                                        @if(auth()->user()->wishlist->contains($item->id))
                                            <i class="bi bi-heart-fill text-danger"></i>
                                        @else
                                            <i class="bi bi-heart text-muted"></i>
                                        @endif
                                    </button>
                                </form>
                            @endif
                        @endauth
                        <div class="card-body">
                            <h5 class="card-title">{{ $item->title }}</h5>
                            <p>€{{ number_format($item->price, 2) }}</p>
                            <a href="{{ route('items.show', $item->id) }}" class="btn btn-primary" data-translate>View</a>
                        </div>
                    </div>
                </div>
            @empty
                <p data-translate>You haven't added anything yet!</p>
            @endforelse
        </div>
    </div>
</x-layout>
