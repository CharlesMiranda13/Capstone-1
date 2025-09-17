<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\UserController; 

// Public Pages
Route::view('/', 'index')->name('home');
Route::view('/loading', 'loading');
Route::view('/about', 'about')->name('about');
Route::view('/services', 'services')->name('services');
Route::view('/pricing', 'pricing')->name('pricing');
Route::view('/ptlist', 'ptlist')->name('ptlist');
Route::view('/contact', 'contact')->name('contact');
Route::view('/therapists', 'therapists')->name('therapists');

// User Authentication
Route::view('/logandsign', 'logandsign')->name('user.auth');

// Multi-type Registration
Route::prefix('register')->group(function () {
    Route::get('/{type}', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
    Route::post('/{type}', [RegisterController::class, 'register'])->name('register.store');
});

// Admin Authentication
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
    Route::delete('/users/{id}/decline', [UserController::class, 'decline'])
        ->name('users.decline')
        ->middleware('auth:admin');
});

// Patient Routes
Route::prefix('patient')->name('user.patients.')->group(function () {
    Route::view('/homepage', 'user.patients.patient')->name('patient');
    Route::view('/appointment', 'user.patients.appointment')->name('appointment');
    Route::view('/records', 'user.patients.records')->name('records');
    Route::view('/setting', 'user.patients.setting')->name('settings');
});


// Therapist Routes
Route::prefix('therapist')->group(function () {
    Route::view('/dashboard', 'user.therapists.dashboard')->name('therapist.home');
    // otw
});

// Clinic Routes
Route::prefix('clinic')->group(function () {
    Route::view('/dashboard', 'user.clinics.dashboard')->name('clinic.home');
    // otw
});

// Verification Routes
Route::prefix('verify')->group(function () {
    Route::get('/', [VerificationController::class, 'show'])->name('verification.notice');
    Route::post('/confirm', [VerificationController::class, 'confirm'])->name('verification.confirm');
    Route::post('/resend', [VerificationController::class, 'resend'])->name('verification.resend');
});
