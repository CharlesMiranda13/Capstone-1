<?php

use Illuminate\Support\Facades\Route;

// Public Pages
Route::get('/', function () {
    return view('index');
});

Route::get('/loading', function () {
    return view('loading');
});

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/services', function () {
    return view('services');
})->name('services');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/pricing', function () {
    return view('pricing');
})->name('pricing');

Route::get('/ptlist', function () {
    return view('ptlist');
})->name('ptlist');

Route::get('/logandsign', function () {
    return view('logandsign');
});

Route::get('/register/therapist', function () {
    return view('register.indtherapist');
});
Route::get('/register/patient', function () {
    return view('register.patientreg');
});

Route::get('/therapists', function () {
    return view('therapists');
})->name('therapists');

Route::get('/login', function () {
    return view('logandsign');
})->name('login');

// Admin Page
Route::get('/user/admin', function () {
    return view('user.admin.admin'); 
})->name('admin.dashboard');


Route::get('/admin/login', function () {
    return view('auth.adminlogin'); 
})->name('admin.login');