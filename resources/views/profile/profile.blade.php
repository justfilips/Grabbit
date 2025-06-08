<x-layout title="Profile">
    <div class="container mt-5">
        <h2>User Profile</h2>

        @auth
            <div class="row mt-4">
                <div class="col-md-4 text-center">
                    @if(Auth::user()->profile_image)
                        <img src="{{ auth()->user()->profile_image }}" alt="Profile Image" class="img-fluid rounded-circle mb-3" style="max-width: 200px; height: 200px; object-fit: cover;">
                    @else
                        <img src="https://i.pinimg.com/474x/47/ba/71/47ba71f457434319819ac4a7cbd9988e.jpg" alt="No Profile Image" class="img-fluid rounded-circle mb-3">
                    @endif
                </div>

                <div class="col-md-8">
                    <h3>{{ Auth::user()->name }}</h3>
                    <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                    <p><strong>Location:</strong> {{ Auth::user()->location ?? 'Not set' }}</p>
                    <p><strong>About Me:</strong><br> {{ Auth::user()->profile_description ?? 'No description provided.' }}</p>
                    <p><strong>Average Rating:</strong> {{ number_format(Auth::user()->average_rating, 2) }}</p>
                    <p><strong>Items Listed:</strong> {{ Auth::user()->items->count() }}</p>
                </div>
            </div>
        @else
            <p>Please <a href="{{ route('login') }}">login</a> to view your profile.</p>
        @endauth
    </div>
</x-layout>
