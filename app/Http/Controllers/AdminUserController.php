<?php
namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use App\Models\ReportedItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AdminUserController extends Controller
{
    use AuthorizesRequests;
    public function panel()
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $users = User::where('role', '!=', 'admin')->get();

        $reportedListings = ReportedItem::with(['item', 'user'])->get();

        return view('admin.panel', compact('users', 'reportedListings'));
    }


    public function promote(User $user)
    {
        $this->authorize('promote', $user);

        $user->role = 'admin';
        $user->save();

        return redirect()->route('admin.panel')->with('success', "{$user->name} has been promoted to admin.");
    }

    public function deleteListingFromPanel(Item $item)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $item->delete();

        return redirect()->route('admin.panel')->with('success', "Listing '{$item->title}' deleted successfully.");
    }

    public function deleteListingFromShow(Item $item)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $item->delete();

        return redirect()->route('home')->with('success', "Listing '{$item->title}' deleted successfully.");
    }

    public function keepListing(ReportedItem $report)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $report->delete();

        return redirect()->route('admin.panel')->with('success', 'Report removed, listing kept.');
    }

    public function deleteUser(User $user)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        if ($user->id === Auth::id() || $user->role === 'admin') {
            return redirect()->back()->with('error', 'You cannot delete this user.');
        }

        $user->delete();

        return redirect()->route('home')->with('success', "User '{$user->name}' deleted.");
    }

}
