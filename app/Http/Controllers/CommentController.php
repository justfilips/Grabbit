<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'item_id' => 'required|exists:items,id',
        ]);

        Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $request->item_id,
            'content' => $request->content,
        ]);

        return back()->with('success', 'Comment added successfully!');
    }
}
