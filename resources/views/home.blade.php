<x-layout title="Home Page">
    <div class="container py-4">
        <h2 class="mb-3">Welcome to Grabbit</h2>

        @auth
            <p>Welcome back, {{ auth()->user()->name }}!</p>
            <a href="{{ route('item.create') }}" class="btn btn-primary mb-4">Create New Item</a>
        @endauth

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
</x-layout>
