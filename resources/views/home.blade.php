<x-layout title="Home Page">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-3 col-lg-2 border-end pe-4">
                <h5 data-translate>Filters</h5>
                <form action="{{ route('home') }}" method="GET">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    
                    <div class="mb-3">
                        <label for="price_min" class="form-label" data-translate>Min Price</label>
                        <input type="number" name="price_min" id="price_min" class="form-control" value="{{ request('min_price') }}">
                        <label for="price_max" class="form-label mt-2" data-translate>Max Price</label>
                        <input type="number" name="price_max" id="price_max" class="form-control" value="{{ request('max_price') }}">
                    </div>

                    <div class="mb-3">
                        <label for="location" class="form-label" data-translate>Location</label>
                        <input type="text" name="location" id="location" class="form-control" value="{{ request('location') }}">
                    </div>

                    <div class="mb-3">
                        <label for="category" class="form-label" data-translate>Category</label>
                        <select name="category" id="category" class="form-select">
                            <option value="">-- Select --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" data-translate>Search</button>
                </form>
            </div>

            <div class="col-md-9 col-lg-10">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    @auth
                        <a href="{{ route('item.create') }}" class="btn btn-success" data-translate>+ Create Item</a>
                    @endauth
                </div>

                @auth
                    <p data-translate>Welcome back,</p> {{ auth()->user()->name }}!
                @endauth

                <div class="container mt-4">
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
    </div>
</x-layout>
