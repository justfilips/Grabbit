<x-layout title="Admin Panel">
    <div class="container mt-4">
        <h2 class="mb-3">Admin Panel</h2>

        {{-- Bootstrap Nav Tabs --}}
        <ul class="nav nav-tabs" id="adminTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">
                    Promote Users
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="reports-tab" data-bs-toggle="tab" data-bs-target="#reports" type="button" role="tab">
                    Reported Listings
                </button>
            </li>
        </ul>

        {{-- Tab Content --}}
        <div class="tab-content mt-3" id="adminTabContent">

            {{-- Promote Users Tab --}}
            <div class="tab-pane fade show active" id="users" role="tabpanel">
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

            <div class="tab-pane fade" id="reports" role="tabpanel">

                {{-- Example layout for future implementation --}}
                @if(isset($reportedListings) && $reportedListings->count())
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Thumbnail</th>
                                <th>Title & Description</th>
                                <th>Reason for Report</th>
                                <th>Reported By</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportedListings as $report)
                                <tr>
                                    <td style="width: 100px;">
                                        @if($report->item && $report->item->images->isNotEmpty())
                                            <img src="{{ asset($report->item->images->first()->image_path) }}" alt="Thumbnail" class="img-thumbnail" style="max-width: 100px;">
                                        @else
                                            <span class="text-muted">No image</span>
                                        @endif

                                    </td>
                                    <td>
                                        <strong>{{ $report->item->title ?? 'Unknown' }}</strong>
                                        <p class="mb-0 text-truncate" style="max-width: 300px;">
                                            {{ Str::limit($report->item->description ?? 'No description', 100) }}
                                        </p>
                                        @if($report->item)
                                            <a href="{{ route('item.show', $report->item->id) }}" target="_blank" class="small">View Listing</a>
                                        @endif
                                    </td>
                                    <td style="max-width: 250px;">{{ $report->reason }}</td>
                                    <td>{{ $report->user->name ?? 'Unknown' }}</td>
                                    <td>{{ $report->item->created_at->format('M d, Y') ?? 'N/A' }}</td>
                                    <td>
                                        {{-- Delete Listing --}}
                                        <form method="POST" action="{{ route('listings.delete', $report->item->id) }}" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger mb-1" onclick="return confirm('Are you sure you want to delete this listing?')">Delete</button>
                                        </form>

                                        {{-- Keep Listing (remove report) --}}
                                        <form method="POST" action="{{ route('listings.keep', $report->id) }}" style="display:inline-block; margin-left: 5px;">
                                            @csrf
                                            <button class="btn btn-sm btn-secondary mb-1">Keep</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                @else
                    <div class="alert alert-secondary">No reported listings found.</div>
                @endif
            </div>

        </div>
    </div>
</x-layout>
