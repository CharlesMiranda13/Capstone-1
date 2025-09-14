<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;

// Public Pages
Route::view('/', 'index')->name('home');
Route::view('/loading', 'loading');
Route::view('/about', 'about')->name('about');
Route::view('/services', 'services')->name('services');
Route::view('/pricing', 'pricing')->name('pricing');
Route::view('/ptlist', 'ptlist')->name('ptlist');
Route::view('/contact', 'contact')->name('contact');
Route::view('/therapists', 'therapists')->name('therapists');

// Patient & Therapist Authentication (Users)
Route::view('/logandsign', 'logandsign')->name('user.auth');
Route::prefix('register')->group(function () {
    Route::view('/therapist', 'register.indtherapist')->name('register.therapist');
    Route::view('/clinic', 'register.clinicreg')->name('register.clinic');
    Route::get('/patient', [RegisterController::class, 'showRegistrationForm'])->name('register.patient');
    Route::post('/patient', [RegisterController::class, 'register'])->name('register.patient.store');
});

// Admin Authentication
Route::prefix('admin')->group(function () {
    Route::view('/login', 'auth.adminlogin')->name('admin.login');
    Route::view('/dashboard', 'user.admin.admin')->name('admin.dashboard');
});

// Patient Routes
Route::prefix('patient')->group(function () {
    Route::view('/homepage', 'user.patients.patient')->name('patient.home');
    Route::view('/appointment', 'user.patients.appointments')->name('patient.appointment');
    Route::view('/records', 'user.patients.records')->name('patient.records');
});
Route::view('/setting', 'user.patients.setting')->name('patient.settings'); 



// Verification Routes
Route::get('/verify', function () {
    return view('auth.reg_verify');
})->name('verification.notice');

Route::post('/verify/confirm', [App\Http\Controllers\Auth\VerificationController::class, 'confirm'])
    ->name('verification.confirm');

Route::post('/verify/resend', [App\Http\Controllers\Auth\VerificationController::class, 'resend'])
    ->name('verification.resend');