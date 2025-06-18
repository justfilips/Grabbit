<x-layout title="{{ $item->title }}">
    <div class="container mt-5">
        <h2>{{ $item->title }}</h2>

        @if($item->images->isNotEmpty())
        <div id="itemImagesCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
            <style>
            .carousel-control-prev-icon,
            .carousel-control-next-icon {
                filter: invert(1); 
            }
            </style>
            <div class="carousel-inner" style="max-height: 400px;">
                @foreach($item->images as $key => $image)
                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                        <img src="{{ $image->image_path }}" class="d-block w-100" alt="{{ $item->title }}" style="object-fit: contain; max-height: 400px;">
                    </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#itemImagesCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#itemImagesCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
        @endif

        <p><strong data-translate>Price:</strong> €{{ number_format($item->price, 2) }}</p>
        <p><strong data-translate>Description:</strong><br>{{ $item->description }}</p>

        @if($item->location)
            <p><strong data-translate>Location (text):</strong> {{ $item->location }}</p>
        @endif

        @if($item->latitude && $item->longitude)
            <h5 class="mt-4" data-translate>Area</h5>
            <div id="map" style="height: 300px;" class="mb-3" aria-label="Map preview"></div>
        @endif

        <a href="{{ route('home') }}" class="btn btn-secondary" data-translate>Back to Listings</a>
    </div>

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

    <!-- Bootstrap JS (make sure Bootstrap 5 JS is included!) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @auth
        <div class="card mt-4">
            <div class="card-header">
                <h5 data-translate>Report this listing</h5>
            </div>
            <div class="card-body">

                <form method="POST" action="{{ route('items.report', $item->id) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="reason" class="form-label" data-translate>Reason for reporting</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger" data-translate>Report</button>
                </form>
            </div>
        </div>
    @endauth

    @guest
        <p>
            <a href="{{ route('login.form') }}" data-translate>Log in</a> <span data-translate>to report this listing.</span>
        </p>
    @endguest

    <div class="container mt-5">
        <h4 data-translate>Comments</h4>

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
                    <label for="content" class="form-label" data-translate>Add a comment</label>
                    <textarea name="content" class="form-control" id="content" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary" data-translate>Post Comment</button>
            </form>
        @else
            <p class="mt-3">
                <span data-translate>Please</span> 
                <a href="{{ route('login') }}" data-translate>login</a> 
                <span data-translate>to comment.</span>
            </p>
        @endauth
    </div>

    <div class="container mt-5">
        @auth
            @if(Auth::user()->isAdmin())
                <form method="POST" action="{{ route('items.delete', $item->id) }}" onsubmit="return confirm('Are you sure you want to delete this listing?')" class="mt-3">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" data-translate>Delete Listing</button>
                </form>
            @endif
        @endauth
    </div>
</x-layout>