<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run()
    {
        if (Setting::count() == 0) {   
            Setting::create([          
                'system_name' => 'HealConnect',                   
                'description' => 'Remote physical therapy platform',  
                'contact_email' => 'support@healconnect.com',       
                'terms' => 'Default terms and conditions here.',
                'privacy' => 'Default privacy policy here.'
            ]);
        }
    }
}
