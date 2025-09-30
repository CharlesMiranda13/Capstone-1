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

/*Public Pages*/

Route::view('/', 'index')->name('home');
Route::view('/loading', 'loading');
Route::view('/about', 'about')->name('about');
Route::view('/services', 'services')->name('services');
Route::view('/pricing', 'pricing')->name('pricing');
Route::view('/contact', 'contact')->name('contact');
Route::get('/ptlist', [PatientController::class, 'publicTherapists'])->name('ptlist');

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

/*Patient Routes*/

Route::prefix('patient')->name('patient.')->middleware(['auth', 'check.status'])->group(function () {
    Route::get('/home', [PatientController::class, 'dashboard'])->name('home');
    Route::view('/appointments', 'user.patients.appointment')->name('appointments');
    Route::view('/records', 'user.patients.records')->name('records');
    Route::view('/messages', 'user.patient.messages')->name('messages');
    Route::view('/settings', 'user.patients.settings')->name('settings');
    Route::get('/therapists', [PatientController::class, 'listOfTherapist'])
        ->name('therapists');

    Route::post('/logout', [App\Http\Controllers\Auth\UserAuthController::class, 'logout'])
    ->name('logout');

});      
/*Therapist Routes*/

Route::prefix('therapist')->name('therapist.')->group(function () {
    Route::get('/home', [IndtherapistController::class, 'dashboard'])->name('home');
    Route::view('/appointments', 'user.therapist.appointment')->name('appointments');
    Route::view('/records', 'user.therapist.records')->name('records');
    Route::view('/messages', 'user.therapist.messages')->name('messages');
    Route::view('/settings', 'user.therapist.settings')->name('settings');
    Route::view('/clients', 'user.therapist.client')->name('client');

    Route::post('/logout', [App\Http\Controllers\Auth\UserAuthController::class, 'logout'])
    ->name('logout');

});

/* Clinic Routes*/
Route::prefix('clinic')->name('clinic.')->group(function () {
    Route::view('/dashboard', 'user.therapist.clinic')->name('dashboard');
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