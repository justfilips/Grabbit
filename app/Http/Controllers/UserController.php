<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    public function showProfile(User $user)
    {   
        return view('profile.profile', compact('user'));
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

        // Update other fields
        $user->name = $validated['name'];
        $user->location = $validated['location'];
        $user->profile_description = $validated['profile_description'];
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
    }

}
