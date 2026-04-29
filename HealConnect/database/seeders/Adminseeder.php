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
            $newAdmin = new User([
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin1234'), // initial password
            ]);
            $newAdmin->role = 'admin';
            $newAdmin->status = 'active';
            $newAdmin->is_verified_by_admin = true;
            $newAdmin->save();
            $this->command->info('Admin created successfully.');
        } else {
            $this->command->info('Admin already exists. Password not changed.');
        }
    }
}
