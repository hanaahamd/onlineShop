<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubCategory;
class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SubCategory::create(['name' => 'Mobile Phones', 'slug' => 'mobile-phones', 'category_id' => 1, 'status' => 1]);
        SubCategory::create(['name' => 'Laptops', 'slug' => 'laptops', 'category_id' => 1, 'status' => 1]);
        SubCategory::create(['name' => 'T-Shirts', 'slug' => 't-shirts', 'category_id' => 2, 'status' => 1]);
    }
}