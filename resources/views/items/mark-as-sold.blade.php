@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Mark "{{ $item->title }}" as Sold</h2>

    <form action="{{ route('items.markSold', $item->id) }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <label for="buyer_id" class="form-label">Select Buyer</label>
            <select name="buyer_id" id="buyer_id" class="form-select" required>
                <option value="">-- Choose buyer --</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Confirm Sale</button>
    </form>
</div>
@endsection
