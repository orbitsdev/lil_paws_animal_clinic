<?php

namespace Database\Seeders;

use App\Models\Animal;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AnimalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $animals = [
            [
                'name' => 'Fluffy',
                'breed' => 'Persian Cat',
                'sex' => 'Female',
                'birth_date' => '2020-05-15',
                'weight' => '4.5 kg',
                'image' => 'fluffy.jpg',
            ],
            [
                'name' => 'Buddy',
                'breed' => 'Golden Retriever',
                'sex' => 'Male',
                'birth_date' => '2019-08-10',
                'weight' => '30.2 kg',
                'image' => 'buddy.jpg',
            ],
            // Add more animals here...
        ];
        
        foreach ($animals as $animalData) {
            Animal::create($animalData);
        }
        
    }
}
