<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@gmail.com'], // unique key to check
            [
                'name' => 'Admin',
                'password' => Hash::make('admin1234'),
                'role' => 'admin', // required field
                'email_verified_at' => now(),
            ]
        );
    }
}
