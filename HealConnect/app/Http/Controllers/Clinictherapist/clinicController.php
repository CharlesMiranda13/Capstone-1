<?php

namespace App\Http\Controllers\Clinictherapist;

use App\Http\Controllers\TherapistController\ptController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\Appointment;
use App\Models\Record;
use App\Models\Referral;
use App\Models\Notification;
use App\Models\User;
use App\Models\Availability;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClinicController extends ptController
{
    public function dashboard()
    {
        $data =$this->getDashboardData();
        return view ('user.therapist.clinic.clinic', $data);

    }

}