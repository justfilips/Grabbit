<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\ItemImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = Category::pluck('name');

        $query = Item::with(['images', 'user']);

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
        
        $items = $query->latest()->get(); // paņem filtrētos ierakstus no datubāzes
        return view('home', compact('items', 'categories')); // atgriež skatu un padod $items uz Blade failu
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
            'location' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'image_path' => 'nullable|image|max:6144',
        ]);

        $item = Item::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'location' => $validated['location'],
            'category_id' => $validated['category_id'],
            'user_id' => 1, // or set to a fixed ID for now like 1
            'status' => 'pending', // default value
            'user_id' => Auth::id(),
        ]);

        if ($request->hasFile('image_path')) {
            $path = $request->file('image_path')->store('item_images', 's3'); // uploads to S3!
            $url = Storage::disk('s3')->url($path); // gets public URL

            ItemImage::create([
                'item_id' => $item->id,
                'image_path' => $url, // save the public URL
            ]);
        }


        return redirect()->route('home')->with('success', 'Item created successfully!');
    }


    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
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


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
