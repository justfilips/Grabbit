<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AdminUserController extends Controller
{
    use AuthorizesRequests;
    public function panel()
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $users = User::where('role', '!=', 'admin')->get();

        return view('admin.panel', compact('users'));
    }


    public function promote(User $user)
    {
        $this->authorize('promote', $user);

        $user->role = 'admin';
        $user->save();

        return redirect()->route('admin.panel')->with('success', "{$user->name} has been promoted to admin.");
    }

}
