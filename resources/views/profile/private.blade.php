<x-layout title="My Profile">
    <div class="container mt-5">
        <h2>My Profile</h2>

        <div class="row mt-4">
            <div class="col-md-4 text-center">
                @if($user->profile_image)
                    <img src="{{ $user->profile_image }}" alt="Profile Image" class="img-fluid rounded-circle mb-3" style="max-width: 200px; height: 200px; object-fit: cover;">
                @else
                    <img src="https://i.pinimg.com/474x/47/ba/71/47ba71f457434319819ac4a7cbd9988e.jpg" alt="No Profile Image" lass="img-fluid rounded-circle mb-3">
                @endif
            </div>

            <div class="col-md-8">
                <h3>{{ $user->name }}</h3>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Location:</strong> {{ $user->location ?? 'Not set' }}</p>
                <p><strong>About Me:</strong><br> {{ $user->profile_description ?? 'No description provided.' }}</p>
                <p><strong>Average Rating:</strong> {{ number_format($user->average_rating, 2) }}</p>
                <p><strong>Items Listed:</strong> {{ $user->items->count() }}</p>

                <a href="{{ route('profile.edit') }}" class="btn btn-primary mt-3">Edit Profile</a>
            </div>
        </div>
    </div>
</x-layout>
