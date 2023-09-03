<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin_role = Role::whereName('Admin')->first();
        $vet_role = Role::whereName('Veterenarian')->first();
        $client_role = Role::whereName('Client')->first();

        $admin_user = User::create([
            'first_name'=> 'Admin ',
            'last_name'=> 'User',
            'email'=> 'admin@gmail.com',
            'role_id'=> $admin_role->id,
            'password'=> Hash::make('password'),
        ]);
        $vet_user = User::create([
            'first_name'=> 'Vet ',
            'last_name'=> 'User',
            'email'=> 'vet@gmail.com',
            'role_id'=> $vet_role->id,
            'password'=> Hash::make('password'),
        ]);
        $client_user = User::create([
            'first_name'=> 'Client ',
            'last_name'=> 'User',
            'email'=> 'client@gmail.com',
            'role_id'=> $client_role->id,
            'password'=> Hash::make('password'),
        ]);

    }
}
