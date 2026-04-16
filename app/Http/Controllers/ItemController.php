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
    {   // No Category table, atgriezt visas 'name' kolonnas
        $categories = Category::pluck('name');
        // Iegust Item parametrus kopa ar attiecīgajiem images un user data, kur status nav sold
        $query = Item::with(['images', 'user'])
            ->where('status', '!=', 'sold');

        // Ja lietotājs izvēlējās meklēt
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Ja lietotājs izvēlējās kategoriju
        if ($request->filled('category')) {
            $category = Category::where('name', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Ja lietotājs izvēlējās cenu ierobezojumus
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Ja lietotājs izvēlējās location filter
        if ($request->filled('lat') && $request->filled('lng')) {

            $lat = (float) $request->lat;
            $lng = (float) $request->lng;
            $radius = (float) ($request->radius ?: 10);

            // converting km to degrees
            $latRange = $radius / 111; // 1 degree of latitude aprox. 111 kilometers
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
            $items = $query->latest()->get(); // sort by latest
        }
        // if user logged in then get contacts, otherwise empty
        $contacts = Auth::check()
            ? $this->getChatContacts()
            : [];

        //load blade file home with these variables available: items, categories, contacts
        return view('home', compact('items', 'categories', 'contacts'));
    }

     // Haversine distance- the great-circle distance between two points on a sphere (such as Earth) given their longitudes and latitudes.
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

    // get create
    public function create()
    {
        $categories = Category::all();
        // pass all categories to items.create
        return view('items.create', compact('categories'));
    }

    // post create (user submitted form)
    public function store(Request $request)
    {
        // ja nesanak validate, tad atsuta atpakal ar errors
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'location' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'image_path' => 'nullable|array',
            'image_path.*' => 'nullable|image|max:6144',
        ]);

        $data = $validated;
        // remove image_path no array, jo items table nav image_path.
        unset($data['image_path']);

        // insert a new row into items table
        $item = Item::create([
            ...$data, // visus validated fields
            'user_id' => Auth::id(), // currently logged in users id
            'status' => 'pending', // default status
        ]);

        // image upload. image_path ir input formas name
        if ($request->hasFile('image_path')) {
            foreach ($request->file('image_path') as $image) {

                if (!$image) continue;

                // $image ir Uploadedfile object
                $path = $image->store('item_images', 's3'); // upload image to s3 and return path
                $url = Storage::disk('s3')->url($path); // convert file path to public url to display on pages

                // create new row in ItemImage with item_id as current items id and path
                ItemImage::create([
                    'item_id' => $item->id,
                    'image_path' => $url,
                ]);
            }
        }
        // redirect, lai nav tas pats request
        return redirect()->route('home')->with('success', 'Item created!');
    }

    public function markAsSold(Request $request, Item $item)
    {
        if (Auth::id() !== $item->user_id) {
            abort(403); // if not owner then u cant do this
        }

        // buyer_id must exist in users table
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
        $user = Auth::user(); // gets current user id

        $ids = Message::where('sender_id', $user->id)
            ->pluck('receiver_id') // all messages where im sender and get the reciever_id's
            ->merge(Message::where('receiver_id', $user->id)->pluck('sender_id')) // all messages where im recievers id
            ->unique();
        // get full user data to all selected users
        return User::whereIn('id', $ids)->get();
    }
}