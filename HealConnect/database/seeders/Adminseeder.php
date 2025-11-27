<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@gmail.com')->first();

        if (!$admin) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin1234'), // initial password
                'role' => 'admin',
                'status' => 'active'
            ]);
            $this->command->info('Admin created successfully.');
        } else {
            $this->command->info('Admin already exists. Password not changed.');
        }
    }
}
