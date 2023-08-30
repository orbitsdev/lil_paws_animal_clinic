<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Vaccine',
                'additional_description' => 'Regular vaccinations to keep pets healthy.',
            ],
            [
                'name' => 'Grooming',
                'additional_description' => 'Bathing, haircuts, and grooming services for pets.',
            ],
            [
                'name' => 'Dental Care',
                'additional_description' => 'Teeth cleaning and oral health checkups for pets.',
            ],
            [
                'name' => 'Boarding',
                'additional_description' => 'Safe and comfortable boarding facilities for pets when owners are away.',
            ],
            [
                'name' => 'Medical Consultation',
                'additional_description' => 'Professional veterinary consultations for pet health concerns.',
            ],
            [
                'name' => 'Surgery',
                'additional_description' => 'Medical procedures and surgeries performed by skilled veterinarians.',
            ],
            [
                'name' => 'Microchipping',
                'additional_description' => 'Implantation of microchips for pet identification and tracking.',
            ],
            [
                'name' => 'Nutrition Counseling',
                'additional_description' => 'Dietary recommendations and nutritional advice for pets.',
            ],
            [
                'name' => 'Behavioral Training',
                'additional_description' => 'Training and guidance to address behavioral issues in pets.',
            ],
            // Add more services as needed
        ];

        foreach($services as $service){
            Service::create($service);
        }
        
    }
}
