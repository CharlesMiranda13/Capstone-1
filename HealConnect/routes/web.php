<?php

use Illuminate\Support\Facades\Route;

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
    Route::view('/patient', 'register.patientreg')->name('register.patient');
});

// Admin Authentication
Route::prefix('admin')->group(function () {
    Route::view('/login', 'auth.adminlogin')->name('admin.login');
    Route::view('/dashboard', 'user.admin.admin')->name('admin.dashboard');
});
