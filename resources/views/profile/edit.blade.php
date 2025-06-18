<x-layout title="Edit Profile">
    <div class="container mt-4">
        <h2 data-translate>Edit Profile</h2>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label" data-translate>Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="mb-3">
                <label for="location" class="form-label" data-translate>Location</label>
                <input type="text" class="form-control" id="location" name="location" value="{{ old('location', $user->location) }}">
            </div>

            <div class="mb-3">
                <label for="profile_description" class="form-label" data-translate>Profile Description</label>
                <textarea class="form-control" id="profile_description" name="profile_description" rows="3">{{ old('profile_description', $user->profile_description) }}</textarea>
            </div>

            <div class="mb-3">
                <label for="profile_image" class="form-label" data-translate>Profile Image</label>
                <input type="file" class="form-control" id="profile_image" name="profile_image">
                @error('profile_image')
                    <div class="alert alert-danger mt-2">{{ $message }}</div>
                @enderror
                @if ($user->profile_image)
                    <div class="mt-2">
                        <img src="{{ $user->profile_image }}" alt="Profile Image" width="150" class="img-thumbnail">
                    </div>
                @endif
            </div>

            <button type="submit" class="btn btn-primary" data-translate>Update Profile</button>
        </form>
    </div>
</x-layout>
