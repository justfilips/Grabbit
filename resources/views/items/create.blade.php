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

                            <div class="mb-2">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control form-control-sm" id="title" name="title" required>
                            </div>

                            <div class="mb-2">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control form-control-sm" id="description" name="description" rows="2" required></textarea>
                            </div>

                            <div class="mb-2">
                                <label for="price" class="form-label">Price (€)</label>
                                <input type="number" class="form-control form-control-sm" id="price" name="price" required min="0" step="0.01">
                            </div>

                            <div class="mb-2">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control form-control-sm" id="location" name="location" required>
                            </div>

                            <div class="mb-2">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select form-select-sm" id="category_id" name="category_id" required>
                                    <option selected disabled value="">Choose...</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-2">
                                <label for="image_path" class="form-label">Item Image</label>
                                <input type="file" class="form-control form-control-sm" id="image_path" name="image_path">
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select form-select-sm" id="status" name="status" required>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="sold">Sold</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-sm btn-primary w-100">Create Item</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
