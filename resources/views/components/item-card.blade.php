@props(['item'])

<div class="card h-100 shadow-sm">
    @if($item->images->isNotEmpty())
        <img src="{{ $item->images->first()->image_path }}" class="card-img-top" alt="{{ $item->title }}">
    @endif

    <div class="card-body d-flex flex-column">
        <h5 class="card-title">{{ $item->title }}</h5>
        <p class="card-text text-muted">{{ Str::limit($item->description, 80) }}</p>
        <p class="fw-bold text-primary">{{ $item->price }} €</p>
        <p class="text-muted"><small>{{ $item->location }}</small></p>

        {{-- Seller info --}}
        @if($item->user)
            <p class="mb-2">
                <small>
                    Sold by: 
                    <a href="{{ route('profile.show', $item->user->id) }}">
                        {{ $item->user->name }}
                    </a>
                </small>
            </p>
        @endif

    </div>
</div>
