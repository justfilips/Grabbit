@props(['item'])

<div class="card h-100 shadow-sm position-relative">

    @if($item->images->isNotEmpty())
        <img src="{{ $item->images->first()->image_path }}"
             class="card-img-top"
             alt="{{ $item->title }}">
    @endif

    @if($item->status === 'sold')
        <span class="badge bg-danger position-absolute m-2">
            Sold
        </span>
    @endif

    @auth
        @if($item->user_id !== auth()->id())
            <form action="{{ route('wishlist.toggle', $item->id) }}"
                  method="POST"
                  class="position-absolute"
                  style="top: 8px; right: 8px;">
                @csrf

                <button type="submit"
                        class="btn btn-light btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                        style="width: 36px; height: 36px;">
                    @if(auth()->user()->wishlist->contains($item->id))
                        <i class="bi bi-heart-fill text-danger"></i>
                    @else
                        <i class="bi bi-heart"></i>
                    @endif
                </button>
            </form>
        @endif
    @endauth

    <div class="card-body d-flex flex-column">

        
        <div>
            <h5 class="card-title">{{ $item->title }}</h5>

            <p class="text-muted mb-1">
                <small>Seller: {{ $item->user->name }}</small>
            </p>

            <p class="text-muted">
                {{ Str::limit($item->description, 80) }}
            </p>

            <p class="fw-bold text-primary mb-1">
                {{ $item->price }} €
            </p>

            <p class="text-muted mb-2">
                <small>{{ $item->location }}</small>
            </p>
        </div>

        <div class="mt-auto">

            <a href="{{ route('item.show', $item->id) }}"
               class="btn btn-outline-primary btn-sm w-100 mb-2">
                View Details
            </a>

            @auth
                @if($item->user_id === auth()->id())

                    @if($item->status !== 'sold')

                        <form action="{{ route('items.markSold', $item->id) }}"
                              method="POST"
                              class="mb-2">
                            @csrf
                            @method('PATCH')

                            <select name="buyer_id"
                                    class="form-select form-select-sm mb-2"
                                    required>
                                <option value="">Select buyer</option>

                                @foreach($contacts ?? [] as $contact)
                                    <option value="{{ $contact->id }}">
                                        {{ $contact->name }}
                                    </option>
                                @endforeach
                            </select>

                            <button class="btn btn-warning btn-sm w-100">
                                Mark as Sold
                            </button>
                        </form>

                        <form action="{{ route('item.destroy', $item) }}"
                              method="POST">
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger btn-sm w-100">
                                Delete Item
                            </button>
                        </form>

                    @else
                        <div class="alert alert-danger py-2 text-center mb-0">
                            Sold
                        </div>
                    @endif

                @endif
            @endauth

        </div>
    </div>
</div>