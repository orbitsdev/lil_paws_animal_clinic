<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\Role;
use App\Models\User;
use App\Models\Clinic;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\AnimalSeeder;
use Database\Seeders\ClinicSeeder;
use Database\Seeders\ServiceSeeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            // ClinicSeeder::class,
            CategorySeeder::class,
            ServiceSeeder::class,
            // AnimalSeeder::class,
        ]);

       

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
