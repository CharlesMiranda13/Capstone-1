<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Auth\AuthController;   
use App\Http\Controllers\Admin\UserController; 
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Patient\PatientController;
use App\Http\Controllers\Indtherapist\IndtherapistController;
use App\Http\Controllers\Clinictherapist\clinicController;
use App\Http\Controllers\TherapistController\ptController;
use App\Http\Controllers\ChatController;
//use App\Http\Controllers\Patient\ReferralController;
use App\Http\Controllers\Patient\AppointmentController;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Models\Setting;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\Admin\AdminContactController;
use App\Http\Controllers\VideoController;

/*Public Pages*/

Route::view('/', 'index')->name('home');
Route::view('/loading', 'loading');
Route::view('/about', 'about')->name('about');
Route::get('/More', function () {
    return view('more');
});
Route::view('/services', 'services')->name('services');
Route::get('/pricing', [SubscriptionController::class, 'index'])->name('pricing.index');
Route::get('/contact', function () {
    $settings = Setting::first(); 
    return view('contact', compact('settings'));
})->name('contact');
Route::post('/contact-submit', [ContactMessageController::class, 'store'])->name('contact.submit');


Route::get('/ptlist', [PatientController::class, 'publicTherapists'])->name('ptlist');
Route::get('/therapists/{id}/profile', [PatientController::class, 'publicTherapistProfile'])
    ->name('view_profile');

Route::get('/terms', function () {
    $settings = \App\Models\Setting::first();
    return view('legal.term&conditions', compact('settings'));
})->name('terms');

Route::get('/privacy-policy', function () {
    $settings = \App\Models\Setting::first();
    return view('legal.privacy_policy', compact('settings'));
})->name('privacy');

/* User Authentication (Patients, Therapists, Clinics)*/

Route::prefix('auth')->group(function () {
    Route::get('/login', [UserAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [UserAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [UserAuthController::class, 'logout'])->name('logout');
});

/* Forgot Password */
Route::prefix('password')->group(function () {
    Route::get('/forgot', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');

    Route::post('/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');

    Route::get('/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');

    Route::post('/reset', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
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
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
        Route::get('viewreports', [AuthController::class, 'reports'])->name('viewreports');
        Route::get('/setting', [AdminSettingsController::class, 'setting'])->name('setting');
        Route::post('/setting', [AdminSettingsController::class, 'update'])->name('setting.update');
        Route::get('/contact-messages', [AdminContactController::class, 'index'])->name('contact_messages');
        Route::get('/contact-messages/{id}', [AdminContactController::class, 'show'])->name('contact_messages.show');
        Route::post('/contact_messages/{id}/reply', [AdminContactController::class, 'reply'])->name('contact_messages.reply');
        Route::get('/users', [UserController::class, 'index'])->name('manage-users');
        Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
        Route::patch('/users/{id}/verify', [UserController::class, 'verify'])->name('users.verify');
        Route::patch('/users/{id}/decline', [UserController::class, 'decline'])->name('users.decline');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/unread-counts', [UserController::class, 'getUnreadCounts'])->name('unread-counts');

        // Subscription Management Routes
        Route::get('/subscriptions', [\App\Http\Controllers\Admin\SubscriptionManagementController::class, 'index'])
            ->name('subscriptions.index');
        Route::get('/subscriptions/{id}', [\App\Http\Controllers\Admin\SubscriptionManagementController::class, 'show'])
            ->name('subscriptions.show');
        Route::patch('/subscriptions/{id}/status', [\App\Http\Controllers\Admin\SubscriptionManagementController::class, 'updateStatus'])
            ->name('subscriptions.updateStatus');
        Route::post('/subscriptions/{id}/activate', [\App\Http\Controllers\Admin\SubscriptionManagementController::class, 'manualActivate'])
            ->name('subscriptions.manualActivate');
        Route::delete('/subscriptions/{id}/cancel', [\App\Http\Controllers\Admin\SubscriptionManagementController::class, 'cancel'])
            ->name('subscriptions.cancel');

        Route::post('/logout', function () {
            auth()->guard('admin')->logout();
            return redirect()->route('admin.login');
        })->name('logout');
    });
});

/* Patient Routes */
Route::prefix('patient')->name('patient.')->middleware(['auth', 'check.status'])->group(function () {
    Route::get('/home', [PatientController::class, 'dashboard'])->name('home');

    //Appointment
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{therapist}/book', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{id}/cancel', [AppointmentController::class, 'cancel'])
        ->name('appointments.cancel');
        
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

    // Patient - View list of medical records
    Route::get('/records', [PatientController::class, 'records'])
        ->name('records');
    
    // Patient - View detailed medical records
    Route::get('/my-records', [PatientController::class, 'myRecords'])
        ->name('my_records');


    // Referral Routes
    //Route::get('/referral/upload', [ReferralController::class, 'create'])->name('referral.upload');
    //Route::post('/referral/upload', [ReferralController::class, 'store'])->name('referral.store');

});


/*Therapist Routes*/

Route::prefix('therapist')->name('therapist.')->middleware(['auth', 'check.status', 'check.subscription'])->group(function () {
    Route::get('/home', [IndtherapistController::class, 'dashboard'])->name('home');
    

    //clients
    Route::get('/clients', [IndtherapistController::class, 'clients'])->name('client');
    Route::get('/patients/{patientId}/records', [ptController::class, 'patient_records'])->name('patients_records');
    Route::put('/patients/{id}/ehr', [ptController::class, 'updateEHR'])->name('ehr.update');
    Route::put('/patients/{id}/treatment', [ptController::class, 'updateTreatment'])->name('treatment.update');
    Route::put('/patients/{id}/progress', [ptController::class, 'updateProgress'])->name('progress.update');

    // Therapist Appointments
    Route::get('/appointments', [App\Http\Controllers\Indtherapist\IndtherapistController::class, 'appointments'])
    ->name('appointments');
    Route::patch('/appointments/{id}/update-status', [IndtherapistController::class, 'updateAppointmentStatus'])->name('appointments.updateStatus');

    Route::get('/services', [IndtherapistController::class, 'services'])->name('services');
    Route::post('/services', [IndtherapistController::class, 'storeServices'])->name('services.store');
    Route::post('/availability', [IndtherapistController::class, 'store'])->name('availability.store');
    Route::patch('/availability/{id}/toggle', [IndtherapistController::class, 'toggleAvailability'])->name('availability.toggle');
    Route::delete('/appointments/{id}', [IndtherapistController::class, 'destroy'])->name('availability.destroy');

    Route::get('/profile', [IndtherapistController::class, 'profile'])->name('profile');

    // Patient Profile
    Route::get('/patients/{id}/profile', [ptController::class, 'patientProfile'])->name('patients.profile');

    //settings
    Route::get('/settings', [IndtherapistController::class, 'settings'])->name('settings');
    Route::put('/settings/profile', [IndtherapistController::class, 'updateProfile'])->name('update.profile');
    Route::put('/settings/info', [IndtherapistController::class, 'updateInfo'])->name('update.info');
    Route::put('/settings/password', [IndtherapistController::class, 'updatePassword'])->name('update.password');

});

/* Clinic Routes */
Route::prefix('clinic')->name('clinic.')->middleware(['auth', 'check.status', 'check.subscription'])->group(function () {
    Route::get('/profile', [ClinicController::class, 'profile'])->name('profile');

    // Dashboard
    Route::get('/home', [ClinicController::class, 'dashboard'])->name('home');

    //services
    Route::get('/services', [ClinicController::class, 'services'])->name('services');
    Route::post('/services', [ClinicController::class, 'storeService'])->name('services.store');
    Route::put('/services/{id}', [ClinicController::class, 'updateService'])->name('services.update');
    Route::delete('/services/{id}', [ClinicController::class, 'destroyService'])->name('services.destroy');

    Route::post('/availability', [ClinicController::class, 'storeSchedule'])->name('schedules.store');
    Route::patch('/availability/{id}/toggle', [ClinicController::class, 'toggleSchedule'])->name('schedules.toggle');
    Route::delete('/availability/{id}', [ClinicController::class, 'destroySchedule'])->name('schedules.destroy');

    // Clients (Patients under this clinic’s therapists)
    Route::get('/clients', [ClinicController::class, 'clients'])->name('clients');

    // Appointments (appointments of clinic's therapists)
    Route::get('/appointments', [ClinicController::class, 'appointments'])->name('appointments');
    Route::patch('/appointments/{id}/status', [ClinicController::class, 'updateAppointmentStatus'])->name('appointments.updateStatus');

    // Patient Records 
    Route::get('/patients/{id}/profile', [ptController::class, 'patientProfile'])->name('patients.profile');
    Route::get('/patients/{patientId}/records', [ptController::class, 'patient_records'])->name('patients_records');
    Route::put('/patients/{id}/ehr', [ptController::class, 'updateEHR'])->name('ehr.update');
    Route::put('/patients/{id}/treatment', [ptController::class, 'updateTreatment'])->name('treatment.update');
    Route::put('/patients/{id}/progress', [ptController::class, 'updateProgress'])->name('progress.update');

    // Employees
    Route::get('/employees', [App\Http\Controllers\Clinictherapist\EmployeeController::class, 'index'])->name('employees');
    Route::post('/employees', [App\Http\Controllers\Clinictherapist\EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/{id}/edit', [App\Http\Controllers\Clinictherapist\EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/{id}', [App\Http\Controllers\Clinictherapist\EmployeeController::class, 'update'])->name('employees.update');
   
    Route::delete('/employees/{id}', [App\Http\Controllers\Clinictherapist\EmployeeController::class, 'destroy'])->name('employees.destroy');
    //Route::get('/employees/{id}/schedule', [App\Http\Controllers\Clinictherapist\EmployeeController::class, 'manageSchedule'])->name('employees.schedule');
    //Route::post('/employees/{id}/schedule', [App\Http\Controllers\Clinictherapist\EmployeeController::class, 'storeSchedule'])->name('employees.schedule.store');
    //Route::delete('/employees/schedule/{scheduleId}', [App\Http\Controllers\Clinictherapist\EmployeeController::class, 'destroySchedule'])->name('employees.schedule.destroy');
    // Settings
    Route::get('/settings', [ClinicController::class, 'settings'])->name('settings');
    Route::put('/settings/profile', [ClinicController::class, 'updateProfile'])->name('update.profile');
    Route::put('/settings/info', [ClinicController::class, 'updateInfo'])->name('update.info');
    Route::put('/settings/password', [ClinicController::class, 'updatePassword'])->name('update.password');
});
//logout
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [UserAuthController::class, 'logout'])->name('logout');
});

Route::get('/check-status', function () {
    return response()->json(['status' => Auth::user()->status]);
})->middleware('auth')->name('check.status');

/* Messages  */
Route::middleware(['auth', 'check.status', 'check.subscription'])->group(function () {
    Route::get('/messages', [ChatController::class, 'index'])->name('messages');
    Route::get('/messages/user-info/{id}', [ChatController::class, 'getUserInfo']);
    Route::get('/messages/fetch', [ChatController::class, 'fetch'])->name('messages.fetch');
    Route::post('/messages/send', [ChatController::class, 'send'])->name('messages.send');
    Route::post('/messages/send-voice', [ChatController::class, 'sendVoice'])->name('messages.sendVoice');
    Route::post('/messages/send-file', [ChatController::class, 'sendFile'])->name('messages.sendFile');
    Route::put('/messages/{id}/edit', [ChatController::class, 'update'])->name('messages.update');
    Route::delete('/messages/{id}', [ChatController::class, 'destroy'])->name('messages.destroy');
    Route::post('/messages/mark-as-read/{userId}', [ChatController::class, 'markAsRead'])->name('messages.markAsRead');

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

Route::middleware(['auth'])->group(function () {
    Route::get('/payment', [SubscriptionController::class, 'showPayment'])->name('payment.show');
    Route::post('/payment/checkout', [SubscriptionController::class, 'createCheckoutSession'])->name('payment.checkout');
    Route::get('/payment/success', [SubscriptionController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment/cancel', [SubscriptionController::class, 'paymentCancel'])->name('payment.cancel');
});

// Subscription required page
Route::get('/subscription/required', function() {
    return view('subscription.required');
})->name('subscription.required');

/*Unread Counts for Notifications*/
Route::middleware('auth')->group(function () {
    Route::get('/patient/unread-counts', [App\Http\Controllers\Patient\PatientController::class, 'getUnreadCounts'])
        ->name('patient.unread.counts');
    
    Route::get('/therapist/unread-counts', [App\Http\Controllers\Indtherapist\IndtherapistController::class, 'getUnreadCounts'])
        ->name('therapist.unread.counts');
    
    Route::get('/clinic/unread-counts', [App\Http\Controllers\Clinictherapist\ClinicController::class, 'getUnreadCounts'])
        ->name('clinic.unread.counts');
});
Broadcast::routes(['middleware' => ['auth:web,admin']]);

    // Start video call
Route::middleware(['auth'])->group(function () {
    Route::post('/video/create-room', [VideoController::class, 'createRoom'])->name('video.create');
    Route::get('/video/room/{room}', [VideoController::class, 'showRoom'])->name('video.room');
    Route::delete('/video/room/{room}', [VideoController::class, 'deleteRoom'])->name('video.delete');
    
});