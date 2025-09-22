<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Auth\AuthController;   
use App\Http\Controllers\Admin\UserController; 
use App\Http\Controllers\SubscriptionController;

/*Public Pages*/

Route::view('/', 'index')->name('home');
Route::view('/loading', 'loading');
Route::view('/about', 'about')->name('about');
Route::view('/services', 'services')->name('services');
Route::view('/pricing', 'pricing')->name('pricing');
Route::view('/ptlist', 'ptlist')->name('ptlist');
Route::view('/contact', 'contact')->name('contact');
Route::view('/therapists', 'therapists')->name('therapists');

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

/*Admin Authentication & Management*/

Route::prefix('admin')->name('admin.')->group(function () {
    // Authentication
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/dashboard', [AuthController::class, 'dashboard'])
        ->name('dashboard')
        ->middleware('auth:admin');
    Route::post('/logout', function () {
        auth()->guard('admin')->logout();   
        return redirect()->route('admin.login');
    })->name('logout');

    // User Management
    Route::get('/users', [UserController::class, 'index'])
        ->name('manage-users')
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

Route::prefix('patient')->name('patient.')->group(function () {
    Route::view('/home', 'user.patients.patient')->name('home');
    Route::view('/appointments', 'user.patients.appointment')->name('appointments');
    Route::view('/records', 'user.patients.records')->name('records');
    Route::view('/messages', 'user.patient.messages')->name('messages');
    Route::view('/settings', 'user.patients.settings')->name('settings');

});       
/*Therapist Routes*/

Route::prefix('therapist')->name('therapist.')->group(function () {
    Route::view('/home', 'user.therapists.dashboard')->name('home');
    //otw
});

/* Clinic Routes*/
Route::prefix('clinic')->name('clinic.')->group(function () {
    Route::view('/home', 'user.clinics.dashboard')->name('home');
    //otw
});

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
