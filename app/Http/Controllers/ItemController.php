<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\ItemImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Message;
use App\Models\User;



class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = Category::pluck('name');

        $query = Item::with(['images', 'user'])
            ->where('status', '!=', 'sold'); // nerāda pārdotās


        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->filled('category')) {
            // Atrodam atbilstošo kategorijas ID pēc nosaukuma
            $category = Category::where('name', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        $contacts = [];

        if (Auth::check()) {
            $contacts = $this->getChatContacts(); // Pievieno šo
        }
        
        $items = $query->latest()->get(); // paņem filtrētos ierakstus no datubāzes
        return view('home', compact('items', 'categories', 'contacts')); // atgriež skatu un padod $items uz Blade failu
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'image_path.*' => 'image|max:6144',
        ], [
            'latitude.required' => 'No location was selected.',
            'longitude.required' => 'Please select a location from the given ones.',
            'image_path.*.max' => 'The image is too large. Maximum 6MB.',
            'image_path.*.image' => 'The file to be uploaded must be an image.',
        ]);

        $item = Item::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'category_id' => $validated['category_id'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'user_id' => 1, // or set to a fixed ID for now like 1
            'status' => 'pending', // default value
            'user_id' => Auth::id(),
        ]);

        if ($request->hasFile('image_path')) {
            foreach ($request->file('image_path') as $image) {
                $path = $image->store('item_images', 's3'); // S3 disks
                $url = Storage::disk('s3')->url($path);

                ItemImage::create([
                    'item_id' => $item->id,
                    'image_path' => $url,
                ]);
            }
        }

        return redirect()->route('home')->with('success', 'Item created successfully!');
    }

    public function getChatContacts()
    {
        $user = Auth::user();

        $contactIds = Message::where('sender_id', $user->id)
                        ->pluck('receiver_id')
                        ->merge(
                            Message::where('receiver_id', $user->id)->pluck('sender_id')
                        )
                        ->unique()
                        ->toArray();

        return User::whereIn('id', $contactIds)->get();
    }


    public function markAsSold(Request $request, Item $item)
    {
        // tikai īpašnieks drīkst atzīmēt kā pārdotu
        if (Auth::id() !== $item->user_id) {
            abort(403); // Nepieļauj piekļuvi citiem
        }

        $request->validate([
            'buyer_id' => 'required|exists:users,id',
        ]);

        $item->status = 'sold';
        $item->buyer_id = $request->buyer_id; // <- šim laukam jābūt datubāzē, ja vēlies saglabāt pircēju
        $item->save();

        return redirect()->route('home')->with('success', 'Item marked as sold.');
    }

    public function purchase(Item $item)
    {
        $item->status = 'sold';
        $item->buyer_id = Auth::id();
        $item->save();

        return redirect()->route('items.index')->with('success', 'Item purchased successfully!');
    }

    public function destroy(Item $item)
    {
        $item->images()->delete();
        $item->delete();

        return redirect()->route('home')->with('success', 'Sludinājums izdzēsts veiksmīgi.');
    }





    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $item = Item::with('images')->findOrFail($id);
        return view('items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function update()
    {
        //
    }   

}
