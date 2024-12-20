<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\SubCategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //      \App\Models\User::factory(10)->create();

        //      \App\Models\User::factory()->create([
        //        'name' => 'Test User',
        //          'email' => 'test@example.com',
        //      ]);
        // App\Models\Category::factory(30)->create();

        // \App\Models\Product::factory(30)->create();
        $this->call(CategorySeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(SubCategorySeeder::class);

        $this->call(ProductsSeeder::class);


    
    
    }
}