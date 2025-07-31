<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vytvořte admin uživatele
        $admin = User::create([
            'name' => 'Admin Uživatel',
            'email' => 'admin@crm.test',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // Vytvořte client uživatele
        $client = User::create([
            'name' => 'Klient Uživatel', 
            'email' => 'klient@crm.test',
            'password' => Hash::make('password'),
        ]);
        $client->assignRole('client');
    }
}
