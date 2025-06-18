@props(['item'])

<div class="card h-100 shadow-sm">
    @if($item->images->isNotEmpty())
        <img src="{{ $item->images->first()->image_path }}" class="card-img-top" alt="{{ $item->title }}">
    @endif

    {{-- Rāda pogu vai statusu, ja ir īpašnieks --}}
    @auth
        @if(Auth::id() === $item->user_id && $item->status !== 'sold')
            <form action="{{ route('items.markSold', $item->id) }}" method="POST" class="mt-2">
                @csrf
                @method('PATCH')

                @if(isset($contacts) && count($contacts) > 0)
                    <select name="buyer_id" class="form-select mb-2" required>
                        <option value="">Select buyer</option>
                        @foreach($contacts as $contact)
                            <option value="{{ $contact->id }}">{{ $contact->name }}</option>
                        @endforeach
                    </select>
                @endif
            </form>
        @elseif($item->status === 'sold')
            <span class="badge bg-danger mt-2">Sold</span>
        @endif
    @endauth

    <div class="card-body flex-column">
        @auth
            @if ($item->user_id !== auth()->id())
                <!-- Sirsniņas forma vai poga -->
                <form action="{{ route('wishlist.toggle', $item->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-link p-0">
                        @if(auth()->user()->wishlist->contains($item->id))
                            <i class="bi bi-heart-fill text-danger"></i>
                        @else
                            <i class="bi bi-heart text-muted"></i>
                        @endif
                    </button>
                </form>
            @endif
    
            @if ($item->user_id === auth()->id())
                <form action="{{ route('item.destroy', $item) }}" method="POST" onsubmit="return confirm('Vai tiešām vēlies dzēst šo sludinājumu?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm">
                        <i class="bi bi-trash"></i> 
                    </button>
                </form>
            @endif
        @endauth

        <h5 class="card-title">{{ $item->title }}</h5>
        <p class="card-text text-muted">{{ Str::limit($item->description, 80) }}</p>
        <p class="fw-bold text-primary">{{ $item->price }} €</p>
        <p class="text-muted"><small>{{ $item->location }}</small></p>

        {{-- Seller info --}}
        @if($item->user)
            <p class="mb-2">
                <small>
                    Sold by: 
                    <a href="{{ route('user.profile', $item->user->id) }}">
                        {{ $item->user->name }}
                    </a>
                </small>
            </p>
        @endif

        <a href="{{ route('item.show', $item->id) }}" class="btn btn-primary mt-auto">View Details</a>
    </div>
</div>
