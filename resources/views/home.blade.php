<x-layout title="Home Page">
<div class="container-fluid py-4">
    <div class="row">

         
        <div class="col-md-3 col-lg-2 border-end pe-4">
            <h5>Filters</h5>

            <form action="{{ route('home') }}" method="GET">

                <input type="hidden" name="search" value="{{ request('search') }}">

                <div class="mb-3">
                    <label class="form-label">Min Price</label>
                    <input type="number" name="price_min" class="form-control"
                           value="{{ request('price_min') }}">

                    <label class="form-label mt-2">Max Price</label>
                    <input type="number" name="price_max" class="form-control"
                           value="{{ request('price_max') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Pick location</label>
                    <div id="map" style="height: 250px; border-radius: 8px;"></div>
                </div>

                <input type="hidden" name="lat" id="lat" value="{{ request('lat') }}">
                <input type="hidden" name="lng" id="lng" value="{{ request('lng') }}">

                <div class="mb-3">
                    <label class="form-label">Radius (km)</label>
                    <input type="number" name="radius" id="radius"
                           class="form-control"
                           value="{{ request('radius', 10) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <option value="">All</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category }}"
                                {{ request('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button class="btn btn-primary w-100">Search</button>
            </form>
        </div>

        <div class="col-md-9 col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-3">

                @auth
                    <a href="{{ route('item.create') }}" class="btn btn-success">
                        + Create Item
                    </a>
                @endauth

            </div>

            @auth
                <p>Welcome back, {{ auth()->user()->name }}!</p>
            @endauth

            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                @foreach ($items as $item)
                    <div class="col">
                        @include('components.item-card', ['item' => $item, 'contacts' => $contacts])
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</div>
</x-layout>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

<script>
const latInput = document.getElementById('lat');
const lngInput = document.getElementById('lng');
const radiusInput = document.getElementById('radius');

let lat = parseFloat(latInput.value);
let lng = parseFloat(lngInput.value);
let radius = parseFloat(radiusInput.value || 10);

let hasCoords = !isNaN(lat) && !isNaN(lng);

const map = L.map('map').setView(
    hasCoords ? [lat, lng] : [56.9496, 24.1052],
    hasCoords ? 11 : 12
);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

let marker;
let circle;

function setMap(lat, lng, radiusKm) {

    latInput.value = lat;
    lngInput.value = lng;
    radiusInput.value = radiusKm;

    if (marker) map.removeLayer(marker);
    if (circle) map.removeLayer(circle);

    marker = L.marker([lat, lng]).addTo(map);

    circle = L.circle([lat, lng], {
        radius: radiusKm * 1000
    }).addTo(map);
}

// restore after refresh
if (hasCoords) {
    setMap(lat, lng, radius);
}

// click map
map.on('click', function(e) {
    setMap(
        e.latlng.lat,
        e.latlng.lng,
        parseFloat(radiusInput.value || 10)
    );
});

// radius change
radiusInput.addEventListener('input', function () {
    if (!marker) return;

    const pos = marker.getLatLng();
    setMap(pos.lat, pos.lng, parseFloat(this.value || 10));
});
</script>