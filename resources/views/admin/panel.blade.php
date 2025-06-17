<x-layout title="Admin Panel">
    <div class="container mt-4">
        <h2 class="mb-3">Promote Users</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Current Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.users.promote', $user->id) }}">
                                @csrf
                                <button class="btn btn-sm btn-success">Promote to Admin</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">No users available for promotion.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layout>
