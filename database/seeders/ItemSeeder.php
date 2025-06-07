<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get an existing user and category to link the item to
        $user = User::first();
        $category = Category::first();

        // Create 5 example items
        for ($i = 1; $i <= 5; $i++) {
            Item::create([
                'user_id' => $user->id,
                'category_id' => $category->id ?? 1,
                'title' => 'Example Item ' . $i,
                'description' => 'This is a description for Example Item ' . $i,
                'price' => rand(10, 100), // random price between 10 and 100
                'location' => $user->location ?? 'Riga', // fallback to 'Riga' if user has no location
                'status' => 'approved',
            ]);
        }
    }
}