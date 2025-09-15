<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\AuthController;

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
Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('admin.dashboard')->middleware('auth:admin');
});

// Patient Routes
Route::prefix('patient')->group(function () {
    Route::view('/homepage', 'user.patients.patient')->name('patient.home');
    Route::view('/appointment', 'user.patients.appointments')->name('patient.appointment');
    Route::view('/records', 'user.patients.records')->name('patient.records');
    Route::view('/setting', 'user.patients.setting')->name('patient.settings');
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
