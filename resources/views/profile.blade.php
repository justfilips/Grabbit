<x-layout title="Profile">
    <div class="container mt-5">
        <h2>User Profile</h2>

        @auth
            <div class="row mt-4">
                <div class="col-md-4 text-center">
                    @if(auth()->user()->profile_image)
                        <img src="{{ auth()->user()->profile_image }}" alt="Profile Image" class="img-fluid rounded-circle mb-3" style="max-width: 200px; height: 200px; object-fit: cover;">
                    @else
                        <img src="https://via.placeholder.com/200?text=No+Image" alt="No Profile Image" class="img-fluid rounded-circle mb-3">
                    @endif
                </div>

                <div class="col-md-8">
                    <h3>{{ auth()->user()->name }}</h3>
                    <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                    <p><strong>Location:</strong> {{ auth()->user()->location ?? 'Not set' }}</p>
                    <p><strong>About Me:</strong><br> {{ auth()->user()->profile_description ?? 'No description provided.' }}</p>
                    <p><strong>Average Rating:</strong> {{ number_format(auth()->user()->average_rating, 2) }}</p>
                    <p><strong>Items Listed:</strong> {{ auth()->user()->items->count() }}</p>
                </div>
            </div>
        @else
            <p>Please <a href="{{ route('login') }}">login</a> to view your profile.</p>
        @endauth
    </div>
</x-layout>
