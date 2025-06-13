<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Item;

class UserController extends Controller
{
    // Skats citam lietotājam (publisks profils)
    public function showProfile(User $user)
    {   
        $items = Item::where('user_id', $user->id)->with('images')->get();
        $reviews = $user->reviewsReceived()->with('reviewer')->latest()->get();
        return view('profile.public', compact('user','items', 'reviews'));
    }

    // Skats sev pašam (mans profils)
    public function myProfile()
    {
        $user = Auth::user();
        $myListings = Item::where('user_id', $user->id)->with('images')->get();
        $boughtItems = $user->boughtItems()->with('images')->get(); // <- Pievienoju ->with('images')
        $reviews = $user->reviewsReceived()->with('reviewer')->latest()->get();
        return view('profile.private', compact('user', 'myListings', 'boughtItems', 'reviews'));
    }


    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'profile_description' => 'nullable|string',
            'profile_image' => 'nullable|image|max:4096', // max 4MB
        ]);

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 's3');
            $url = Storage::disk('s3')->url($path);
            $user->profile_image = $url;
        }

        $user->name = $validated['name'];
        $user->location = $validated['location'];
        $user->profile_description = $validated['profile_description'];
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
    }
    

}
