<x-layout title="Home Page">
    <div class="container-fluid py-4">
        <div class="row">
            {{-- Left Sidebar: Filters --}}
            <div class="col-md-3 col-lg-2 border-end pe-4">
                <h5>Filters</h5>
                <form action="{{ route('home') }}" method="GET">
                    {{-- Lai saglabātu search results --}}
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    {{-- Price --}}
                    <div class="mb-3">
                        <label for="price_min" class="form-label">Min Price</label>
                        <input type="number" name="price_min" id="price_min" class="form-control" value="{{ request('min_price') }}">
                        <label for="price_max" class="form-label mt-2">Max Price</label>
                        <input type="number" name="price_max" id="price_max" class="form-control" value="{{ request('max_price') }}">
                    </div>

                    {{-- Location --}}
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" name="location" id="location" class="form-control" value="{{ request('location') }}">
                    </div>

                    {{-- Category --}}
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select name="category" id="category" class="form-select">
                            <option value="">-- Select --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </form>
            </div>

            <div class="col-md-9 col-lg-10">
                {{-- Create button --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    @auth
                        <a href="{{ route('item.create') }}" class="btn btn-success">+ Create Item</a>
                    @endauth
                </div>

                {{-- Greeting --}}
                @auth
                    <p>Welcome back, {{ auth()->user()->name }}!</p>
                @endauth

                {{-- Item grid --}}
                <div class="row">
                    @forelse($items as $item)
                        <div class="col-md-4 mb-4">
                            <x-item-card :item="$item" />
                        </div>
                    @empty
                        <p>No items found.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layout>
