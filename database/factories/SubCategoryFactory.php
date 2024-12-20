<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubCategory>
 */
class SubCategoryFactory extends Factory
{
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word, // اسم الفئة الفرعية
            'slug' => $this->faker->slug, // عنوان URL للفئة
            'category_id' => rand(1, 10), // اختياري: استبدل هذا بالقيمة الصحيحة لفئة رئيسية موجودة
            'status' => $this->faker->randomElement([0, 1]), // الحالة (مفعل أو غير مفعل)
        ];
    }
}