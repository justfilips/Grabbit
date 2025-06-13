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
            <h5 class="mt-4">Approximate Area</h5>
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
</x-layout>
