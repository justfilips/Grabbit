<?php
namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ReportedItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportedItemController extends Controller
{
    public function store(Request $request, Item $item)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $alreadyReported = ReportedItem::where('item_id', $item->id)->where('reported_by', Auth::id())->exists();

        if ($alreadyReported) {
            return back()->with('error', 'You have already reported this item.');
        }

        ReportedItem::create([
            'item_id' => $item->id,
            'reported_by' => Auth::id(),
            'reason' => $request->reason,
        ]);

        return back()->with('success', 'Thank you for reporting this item. We will review it shortly.');
    }
}
