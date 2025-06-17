<x-layout title="{{ $item->title }}">
    <div class="container mt-5">
        <h2>{{ $item->title }}</h2>

        @if($item->images->isNotEmpty())
            <img src="{{ $item->images->first()->image_path }}" alt="{{ $item->title }}" class="img-fluid mb-4" style="max-height: 400px; object-fit: contain;">
        @endif

        <p><strong>Price:</strong> €{{ number_format($item->price, 2) }}</p>
        <p><strong>Description:</strong><br>{{ $item->description }}</p>

        @if($item->location)
            <p><strong>Location (text):</strong> {{ $item->location }}</p>
        @endif

        @if($item->latitude && $item->longitude)
            <h5 class="mt-4">Area</h5>
            <div id="map" style="height: 300px;" class="mb-3"></div>
        @endif

        <a href="{{ route('home') }}" class="btn btn-secondary">Back to Listings</a>
    </div>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

    @if($item->latitude && $item->longitude)
        <script>
            const lat = {{ $item->latitude }};
            const lng = {{ $item->longitude }};

            const map = L.map('map').setView([lat, lng], 10);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            L.marker([lat, lng]).addTo(map)
                .openPopup();

            L.circle([lat, lng], {
                color: 'blue',
                fillColor: '#cce5ff',
                fillOpacity: 0.4,
                radius: 5000
            }).addTo(map);
        </script>
    @endif
    @auth
        <div class="card mt-4">
            <div class="card-header">
                <h5>Report this listing</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form method="POST" action="{{ route('items.report', $item->id) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason for reporting</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">Report</button>
                </form>
            </div>
        </div>
    @endauth

@guest
    <p><a href="{{ route('login.form') }}">Log in</a> to report this listing.</p>
@endguest

    <!-- Komentāri -->
    <div class="container mt-5">
        <h4>Comments</h4>

        @foreach($item->comments as $comment)
            <div class="mb-3 p-3 border rounded">
                <strong>{{ $comment->user->name }}</strong>
                <p class="mb-0">{{ $comment->content }}</p>
                <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
            </div>
        @endforeach

        @auth
            <form action="{{ route('comments.store') }}" method="POST" class="mt-4">
                @csrf
                <input type="hidden" name="item_id" value="{{ $item->id }}">
                <div class="mb-3">
                    <label for="content" class="form-label">Add a comment</label>
                    <textarea name="content" class="form-control" id="content" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Post Comment</button>
            </form>
        @else
            <p class="mt-3">Please <a href="{{ route('login') }}">login</a> to comment.</p>
        @endauth
    </div>
    <div class="container mt-5">
        @auth
            @if(Auth::user()->isAdmin())
                <form method="POST" action="{{ route('items.delete', $item->id) }}" onsubmit="return confirm('Are you sure you want to delete this listing?')" class="mt-3">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger">Delete Listing</button>
                </form>
            @endif
        @endauth

    </div>


</x-layout>
