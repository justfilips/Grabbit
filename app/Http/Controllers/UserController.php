<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function showProfile()
    {
        // $user = Auth::user();
        return view('profile.profile');
    }

    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'profile_description' => 'nullable|string',
            'profile_image' => 'nullable|image|max:2048', // max 2MB
        ]);

        // Handle profile image upload if exists
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 's3');
            $url = Storage::disk('s3')->url($path);
            $user->profile_image = $url;
        }

        // Update other fields
        $user->name = $validated['name'];
        $user->location = $validated['location'];
        $user->profile_description = $validated['profile_description'];
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
    }

}
