<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:isAdmin');
    }

    public function index()
    {
        $users = User::where('role', '!=', 'admin')->get();
        return view('admin.users.index', compact('users'));
    }

    public function promote(User $user)
    {
        $this->authorize('promote', $user);

        $user->role = 'admin';
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User promoted to admin.');
    }
}
