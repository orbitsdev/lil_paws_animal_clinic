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
        $vet_role = Role::whereName('Vet')->first();
        $client_role = Role::whereName('Client')->first();

        $admin_user = User::create([
            'name'=> 'Admin User',
            'email'=> 'admin@gmail.com',
            'role_id'=> $admin_role->id,
            'password'=> Hash::make('password'),
        ]);
        $vet_user = User::create([
            'name'=> 'Vet User',
            'email'=> 'vet@gmail.com',
            'role_id'=> $vet_role->id,
            'password'=> Hash::make('password'),
        ]);
        $client_user = User::create([
            'name'=> 'Client User',
            'email'=> 'client@gmail.com',
            'role_id'=> $client_role->id,
            'password'=> Hash::make('password'),
        ]);

    }
}
