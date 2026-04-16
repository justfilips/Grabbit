<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\ItemImage;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::pluck('name');

        $query = Item::with(['images', 'user'])
            ->where('status', '!=', 'sold');

        // SEARCH
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // CATEGORY
        if ($request->filled('category')) {
            $category = Category::where('name', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // PRICE
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // RADIUS FILTER
        if ($request->filled('lat') && $request->filled('lng')) {

            $lat = (float) $request->lat;
            $lng = (float) $request->lng;
            $radius = (float) ($request->radius ?: 10);

            $latRange = $radius / 111;
            $lngRange = $radius / (111 * cos(deg2rad($lat)));

            $items = $query
                ->whereBetween('latitude', [$lat - $latRange, $lat + $latRange])
                ->whereBetween('longitude', [$lng - $lngRange, $lng + $lngRange])
                ->get()
                ->filter(function ($item) use ($lat, $lng, $radius) {
                    return $this->distanceKm(
                        $lat,
                        $lng,
                        $item->latitude,
                        $item->longitude
                    ) <= $radius;
                });

        } else {
            $items = $query->latest()->get();
        }

        $contacts = Auth::check()
            ? $this->getChatContacts()
            : [];

        return view('home', compact('items', 'categories', 'contacts'));
    }

    /**
     * Haversine distance
     */
    private function distanceKm($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function create()
    {
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'location' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',

            // FIXED
            'image_path' => 'nullable|array',
            'image_path.*' => 'nullable|image|max:6144',
        ]);

        // REMOVE image array from item table (IMPORTANT FIX)
        $data = $validated;
        unset($data['image_path']);

        $item = Item::create([
            ...$data,
            'user_id' => Auth::id(),
            'status' => 'pending',
        ]);

        // SAFE IMAGE UPLOAD
        if ($request->hasFile('image_path')) {
            foreach ($request->file('image_path') as $image) {

                if (!$image) continue;

                $path = $image->store('item_images', 's3');
                $url = Storage::disk('s3')->url($path);

                ItemImage::create([
                    'item_id' => $item->id,
                    'image_path' => $url,
                ]);
            }
        }

        return redirect()->route('home')->with('success', 'Item created!');
    }

    public function markAsSold(Request $request, Item $item)
    {
        if (Auth::id() !== $item->user_id) {
            abort(403);
        }

        $request->validate([
            'buyer_id' => 'required|exists:users,id',
        ]);

        $item->update([
            'status' => 'sold',
            'buyer_id' => $request->buyer_id,
        ]);

        return redirect()->route('home');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('home');
    }

    public function show($id)
    {
        $item = Item::with('images')->findOrFail($id);
        return view('items.show', compact('item'));
    }

    private function getChatContacts()
    {
        $user = Auth::user();

        $ids = Message::where('sender_id', $user->id)
            ->pluck('receiver_id')
            ->merge(Message::where('receiver_id', $user->id)->pluck('sender_id'))
            ->unique();

        return User::whereIn('id', $ids)->get();
    }
}