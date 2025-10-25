<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Auth\AuthController;   
use App\Http\Controllers\Admin\UserController; 
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Patient\PatientController;
use App\Http\Controllers\Indtherapist\IndtherapistController;
use App\Http\Controllers\Clinictherapist\clinicController;
//use App\Http\Controllers\Patient\ReferralController;
use App\Http\Controllers\Patient\AppointmentController;


/*Public Pages*/

Route::view('/', 'index')->name('home');
Route::view('/loading', 'loading');
Route::view('/about', 'about')->name('about');
Route::view('/services', 'services')->name('services');
Route::view('/pricing', 'pricing')->name('pricing');
Route::view('/contact', 'contact')->name('contact');
Route::get('/ptlist', [PatientController::class, 'publicTherapists'])->name('ptlist');
Route::get('/therapists/{id}/profile', [PatientController::class, 'publicTherapistProfile'])
    ->name('view_profile');

/* User Authentication (Patients, Therapists, Clinics)*/

Route::prefix('auth')->group(function () {
    Route::get('/login', [UserAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [UserAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [UserAuthController::class, 'logout'])->name('logout');
});


Route::view('/logandsign', 'logandsign')->name('user.auth');


/*Registration (Multi-type: Patient, Therapist, Clinic)*/

Route::prefix('register')->group(function () {
    Route::get('/{type}', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
    Route::post('/{type}', [RegisterController::class, 'register'])->name('register.store');
});

// Account Pending Page
Route::get('/account/pending', function () {
    return view('auth.pending');
})->name('account.pending');

/*Admin Authentication & Management*/

Route::prefix('admin')->name('admin.')->group(function () {
    // Authentication
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/dashboard', [UserController::class, 'dashboard'])
        ->name('dashboard')
        ->middleware('auth:admin');
    Route::get('viewreports', [AuthController::class, 'reports'])
        ->name('viewreports')
        ->middleware('auth:admin');
    Route::get('setting', [AuthController::class, 'setting'])
        ->name('setting')
        ->middleware('auth:admin');
    
    Route::post('/logout', function () {
        auth()->guard('admin')->logout();   
        return redirect()->route('admin.login');
    })->name('logout');

    // User Management
    Route::get('/users', [UserController::class, 'index'])
        ->name('manage-users')
        ->middleware('auth:admin');

    Route::get('/users/{id}',[UserController::class, 'show'])
        ->name('users.show')
        ->middleware('auth:admin');

    Route::patch('/users/{id}/verify', [UserController::class, 'verify'])
        ->name('users.verify')
        ->middleware('auth:admin');
    Route::patch('/users/{id}/decline', [UserController::class, 'decline'])
        ->name('users.decline')
        ->middleware('auth:admin');

    Route::get('/users/{id}/edit', [UserController::class, 'edit'])
        ->name('users.edit')
        ->middleware('auth:admin');

    Route::put('/users/{id}', [UserController::class, 'update'])
        ->name('users.update')
        ->middleware('auth:admin');

    Route::delete('/users/{id}', [UserController::class, 'destroy'])
        ->name('users.destroy')
        ->middleware('auth:admin');
});

/* Patient Routes */
Route::prefix('patient')->name('patient.')->middleware(['auth', 'check.status'])->group(function () {
    Route::get('/home', [PatientController::class, 'dashboard'])->name('home');
    Route::view('/records', 'user.patients.records')->name('records');

    // Chat
    Route::get('/messages', [ChatController::class, 'index'])
        ->middleware(['auth', 'role:patient'])
        ->name('messages');

    //Appointment
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{therapist}/book', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');

    // Settings 
    Route::get('/settings', [PatientController::class, 'settings'])->name('settings');
    Route::put('/settings/profile', [PatientController::class, 'updateProfile'])->name('update.profile');
    Route::put('/settings/info', [PatientController::class, 'updateInfo'])->name('update.info');
    Route::put('/settings/password', [PatientController::class, 'updatePassword'])->name('update.password');

    // Therapist list
    Route::get('/therapists', [App\Http\Controllers\Patient\PatientController::class, 'listOfTherapist'])
    ->name('therapists');
    Route::get('/therapists/{id}/profile', [PatientController::class, 'showProfile'])
    ->name('therapists.profile');


    // Referral Routes
    //Route::get('/referral/upload', [ReferralController::class, 'create'])->name('referral.upload');
    //Route::post('/referral/upload', [ReferralController::class, 'store'])->name('referral.store');

    // Logout
    Route::post('/logout', [App\Http\Controllers\Auth\UserAuthController::class, 'logout'])->name('logout');
});


/*Therapist Routes*/

Route::prefix('therapist')->name('therapist.')->middleware(['auth', 'check.status'])->group(function () {
    Route::get('/home', [IndtherapistController::class, 'dashboard'])->name('home');
    Route::view('/records', 'user.therapist.records')->name('records');

    // Chat
    Route::get('/therapist/messages', [ChatController::class, 'index'])
    ->middleware(['auth'])
    ->name('messages');


    Route::view('/clients', 'user.therapist.client')->name('client');

    // Therapist Appointments
    Route::get('/appointments', [App\Http\Controllers\Indtherapist\IndtherapistController::class, 'appointments'])
    ->name('appointments');
    Route::patch('/appointments/{id}/update-status', [IndtherapistController::class, 'updateAppointmentStatus'])->name('appointments.updateStatus');

    Route::get('/services', [IndtherapistController::class, 'services'])->name('services');
    Route::post('/services', [IndtherapistController::class, 'storeServices'])->name('services.store');
    Route::get('/availability', [IndtherapistController::class, 'availability'])->name('availability');   
    Route::post('/availability', [IndtherapistController::class, 'store'])->name('availability.store');
    Route::patch('/availability/{id}/toggle', [IndtherapistController::class, 'toggleAvailability'])->name('availability.toggle');
    Route::delete('/appointments/{id}', [IndtherapistController::class, 'destroy'])->name('availability.destroy');

    Route::get('/profile', [IndtherapistController::class, 'profile'])->name('profile');

    // Patient Profile
    Route::get('/patients/{id}/profile', [IndtherapistController::class, 'patientProfile'])->name('patients.profile');


    Route::get('/settings', [IndtherapistController::class, 'settings'])->name('settings');
    Route::put('/settings/profile', [IndtherapistController::class, 'updateProfile'])->name('update.profile');
    Route::put('/settings/info', [IndtherapistController::class, 'updateInfo'])->name('update.info');
    Route::put('/settings/password', [IndtherapistController::class, 'updatePassword'])->name('update.password');


    Route::post('/logout', [App\Http\Controllers\Auth\UserAuthController::class, 'logout'])
    ->name('logout');

});

/* Clinic Routes*/
Route::prefix('clinic')->name('clinic.')->middleware(['auth', 'check.status'])->group(function () {
    Route::get('/home', [clinicController::class, 'dashboard'])->name('home');
    Route::view('/employees', 'user.therapist.employees')->name('employees');
    Route::view('/appointments', 'user.therapist.appointment')->name('appointments');
    Route::view('/services', 'user.therapist.services')->name('services');
    Route::view('/records', 'user.therapist.records')->name('records');
    Route::view('/settings', 'user.therapist.setting')->name('settings');
});

Route::get('/check-status', function () {
    return response()->json(['status' => Auth::user()->status]);
})->middleware('auth')->name('check.status');


/* Email Verification*/
Route::prefix('verify')->name('verification.')->group(function () {
    Route::get('/', [VerificationController::class, 'show'])->name('notice');
    Route::post('/confirm', [VerificationController::class, 'confirm'])->name('confirm');
    Route::post('/resend', [VerificationController::class, 'resend'])->name('resend');
});

/*Subscription / Pricing Plans*/
Route::prefix('subscribe')->name('subscribe.')->group(function () {
    Route::get('/{plan}', [SubscriptionController::class, 'show'])->name('show');
    Route::post('/{plan}', [SubscriptionController::class, 'store'])->name('store');
});