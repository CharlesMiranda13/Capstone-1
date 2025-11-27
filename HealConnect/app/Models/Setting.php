<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'system_name',
        'logo',
        'contact_email',
        'description',
        'terms',
        'privacy',
        'policies',
        'subscription_fee',
        'max_therapists',
        'payment_gateway',
        'auto_suspend',
    ];
}
