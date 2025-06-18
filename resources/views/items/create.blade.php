<x-layout title="Create">
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4 class="mb-0">Create Item</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('item.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                             @if ($errors->any())
                                <div class="alert alert-danger">
                                    <strong>Fix errors:</strong>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif


                            <div class="mb-2">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control form-control-sm" id="title" name="title" required value="{{ old('title') }}">
                            </div>

                            <div class="mb-2">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control form-control-sm" id="description" name="description" rows="2" required>{{ old('description') }}</textarea>
                            </div>

                            <div class="mb-2">
                                <label for="price" class="form-label">Price (€)</label>
                                <input type="number" class="form-control form-control-sm" id="price" name="price" required min="0" step="0.01" value="{{ old('price') }}">
                            </div>

                            <!-- Address autocomplete input -->
                            <div class="mb-2 position-relative">
                                <label for="location" class="form-label">Location (Address)</label>
                                <input type="text" class="form-control form-control-sm" id="location" name="location" autocomplete="off" required value="{{ old('location') }}">
                                <!-- Autocomplete dropdown will be appended here -->
                            </div>

                            <!-- Hidden latitude & longitude fields -->
                            <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                            <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">

                            <!-- Map preview -->
                            <div id="map" style="height: 300px;" class="mb-3"></div>

                            <div class="mb-2">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select form-select-sm" id="category_id" name="category_id" required>
                                    <option selected disabled value="">Choose...</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-2">
                                <label for="image_path" class="form-label">Item Image</label>
                                <input type="file" name="image_path[]" multiple accept="image/*">
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary w-100">Create Item</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

    <script>
        const map = L.map('map').setView([56.9496, 24.1052], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        let marker;

        const addressInput = document.getElementById('location');
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');

        // Debounce helper function
        function debounce(func, timeout = 300){
            let timer;
            return (...args) => {
                clearTimeout(timer);
                timer = setTimeout(() => { func.apply(this, args); }, timeout);
            };
        }

        // Create autocomplete dropdown container
        const resultsDropdown = document.createElement('div');
        resultsDropdown.style.position = 'absolute';
        resultsDropdown.style.background = '#fff';
        resultsDropdown.style.border = '1px solid #ccc';
        resultsDropdown.style.zIndex = '1000';
        resultsDropdown.style.width = addressInput.offsetWidth + 'px';
        resultsDropdown.style.maxHeight = '150px';
        resultsDropdown.style.overflowY = 'auto';
        resultsDropdown.style.cursor = 'pointer';

        // Append dropdown to input parent
        addressInput.parentNode.style.position = 'relative';
        addressInput.parentNode.appendChild(resultsDropdown);

        addressInput.addEventListener('input', debounce(() => {
            const query = addressInput.value.trim();
            if(query.length < 3){
                resultsDropdown.innerHTML = '';
                return;
            }
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    resultsDropdown.innerHTML = '';
                    data.forEach(place => {
                        const option = document.createElement('div');
                        option.textContent = place.display_name;
                        option.style.padding = '5px';
                        option.addEventListener('click', () => {
                            addressInput.value = place.display_name;
                            latInput.value = place.lat;
                            lngInput.value = place.lon;

                            // Update map view and marker
                            map.setView([place.lat, place.lon], 13);
                            if(marker){
                                marker.setLatLng([place.lat, place.lon]);
                            } else {
                                marker = L.marker([place.lat, place.lon]).addTo(map);
                            }
                            resultsDropdown.innerHTML = '';
                        });
                        resultsDropdown.appendChild(option);
                    });
                });
        }));

        // Hide dropdown if user clicks outside
        document.addEventListener('click', (e) => {
            if (!addressInput.contains(e.target)) {
                resultsDropdown.innerHTML = '';
            }
        });

        // Show marker if lat/lng exist on page load (e.g. after validation error)
        window.addEventListener('load', () => {
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);
            if(lat && lng){
                map.setView([lat, lng], 13);
                marker = L.marker([lat, lng]).addTo(map);
            }
        });
    </script>
</x-layout>
