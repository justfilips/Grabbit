<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    public function panel()
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $users = \App\Models\User::where('role', '!=', 'admin')->get();

        return view('admin.panel', compact('users'));
    }


    public function promote(\App\Models\User $user)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $user->role = 'admin';
        $user->save();

        return redirect()->route('admin.panel')->with('success', "{$user->name} has been promoted to admin.");
    }

}
