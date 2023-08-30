<?php

namespace Database\Seeders;

use App\Models\Clinic;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ClinicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $clinics = [
            [
                'name'=> 'Isulan Clinic',
                'address'=> 'Isulan',

            ],
            [
            'name'=> 'Tacurong Clinic',
                'address'=> 'Tacurong',

            ],
            [
                'name'=> 'Marbel Clinic',
                'address'=> 'Marbel',

            ],
        ];
        foreach($clinics as $clinic){
            Clinic::create($clinic);   
        }  
    }
}
