<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Electronics',
            'Furniture',
            'Clothing',
            'Books',
            'Toys',
            'Sports Equipment',
            'Home Appliances',
            'Garden & Outdoors',
            'Beauty & Health',
            'Automotive',
            'Office Supplies',
            'Pet Supplies',
            'Jewelry',
            'Musical Instruments',
            'Other'
        ];


        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
            ]);
        }
    }
}