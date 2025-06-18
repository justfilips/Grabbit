<x-layout title="Review">
    <div class="container">
        <h2 data-translate>Rate the Seller</h2>

        <form method="POST" action="{{ route('reviews.store') }}">
            @csrf

            <input type="hidden" name="item_id" value="{{ $item_id }}">
            <input type="hidden" name="reviewed_id" value="{{ $reviewed_id }}">

            <div class="mb-3">
                <label for="rating" class="form-label" data-translate>Rating (1 to 5):</label>
                <input type="number" name="rating" id="rating" class="form-control" min="1" max="5" required>
            </div>

            <div class="mb-3">
                <label for="comment" class="form-label" data-translate>Comment (optional):</label>
                <textarea name="comment" id="comment" class="form-control" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary" data-translate>Submit Rating</button>
        </form>
    </div>
</x-layout>
