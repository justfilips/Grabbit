<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function store(Item $item)
    {
        Wishlist::firstOrCreate([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
        ]);

        return back()->with('success', 'Item added to wishlist!');
    }

    public function destroy(Item $item)
    {
        Wishlist::where('user_id', Auth::id())
            ->where('item_id', $item->id)
            ->delete();

        return back()->with('success', 'Item removed from wishlist!');
    }

    public function toggle(Item $item)
    {
        $user = Auth::user();

        if ($user->wishlist()->where('item_id', $item->id)->exists()) {
            $user->wishlist()->detach($item->id);
        } else {
            $user->wishlist()->attach($item->id);
        }

        return back();
    }

    public function index()
    {
        $wishlistItems = Auth::user()->wishlist()->with('images')->get();
        return view('wishlist.index', compact('wishlistItems'));
    }
}

