<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\ItemImage;
use App\Models\ImageImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('home');
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
            $path = $request->file('image_path')->store('item_images', 'public');

            ItemImage::create([
                'item_id' => $item->id,
                'image_path' => $path,
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
