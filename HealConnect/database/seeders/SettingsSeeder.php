<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Settings;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        // Check if a settings record already exists
        if (Settings::count() == 0) {
            Settings::create([
                'system_name' => 'HealConnect',                   
                'description' => 'Remote physical therapy platform',  
                'contact_email' => '@healconnect.com',       
                'terms' => 'Default terms and conditions here.',
                'privacy' => 'Default privacy policy here.'
            ]);
        }
    }
}
