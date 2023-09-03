<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['Dog', 'Cat', 'Hamster', 'Rabbit', 'Monkey'];
        $createdAt = now()->timezone('Asia/Manila');
        $updatedAt = now()->timezone('Asia/Manila');
        foreach($categories as $category){
            Category::create([
                'name'=> $category,
                'created_at'=> $createdAt,
                'updated_at'=> $updatedAt,
            ]);
        }
    }
}
