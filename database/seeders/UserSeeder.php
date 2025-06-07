<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => 'User ' . $i,
                'email' => 'user' . $i . '@example.com',
                'password' => Hash::make('password'), // simple password for testing
                'location' => 'Riga',
                'profile_description' => 'This is the profile description for User ' . $i,
                'profile_image' => null, // you can later upload profile images
                'average_rating' => rand(0, 5),
                'role' => 'user',
            ]);
        }
    }
}