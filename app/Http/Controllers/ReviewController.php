<?php

namespace App\Http\Controllers;

use App\Models\Review;;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ReviewController extends Controller
{
    public function create(Request $request)
    {
        return view('reviews.create', [
            'item_id' => $request->item_id,
            'reviewed_id' => $request->reviewed_id,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'reviewed_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        // Saglabā jauno vērtējumu
        Review::create([
            'reviewer_id' => Auth::id(),
            'reviewed_id' => $request->reviewed_id,
            'item_id' => $request->item_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        // Aprēķina jauno vidējo vērtējumu
        $average = Review::where('reviewed_id', $request->reviewed_id)->avg('rating');

        // Atjaunina lietotāja profilu ar jauno vērtējumu
        $user = User::find($request->reviewed_id);
        $user->average_rating = $average;
        $user->save();

        return redirect()->route('user.profile', $request->reviewed_id);
    }
}
