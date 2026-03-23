<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Dashboard (หน้า Home หลัก)
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [ActivityController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Activity + Registration
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // แสดงกิจกรรมทั้งหมด
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities');

    // ดูรายละเอียดกิจกรรม
    Route::get('/activities/{id}', [ActivityController::class, 'show'])->name('activities.show');

    // สร้างกิจกรรม
    Route::get('/create-activity', [ActivityController::class, 'create'])->name('activities.create');
    Route::post('/create-activity', [ActivityController::class, 'store'])->name('activities.store');

    // สมัครกิจกรรม (ใช้ตัวนี้ตัวเดียว)
    Route::post('/activities/{id}/register', [RegistrationController::class,'register'])->name('activities.register');

    // หน้า My Activities
    Route::get('/my-activities', [RegistrationController::class,'myActivities'])->name('My-activities');
    // My club
    Route::get('/my-created-activities', [ActivityController::class, 'myActivities']);
});

/*
|--------------------------------------------------------------------------
| Admin Management 
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // จัดการการสมัคร (Registrations)
    Route::get('/admin/registrations', [RegistrationController::class, 'adminIndex']);
    Route::post('/admin/registrations/{id}/approve', [RegistrationController::class, 'approve']);
    Route::post('/admin/registrations/{id}/reject', [RegistrationController::class, 'reject']);

    // จัดการกิจกรรม (Activities) 
    Route::get('/admin/activities', [ActivityController::class, 'adminIndex']);
    Route::post('/admin/activities/{id}/approve', [ActivityController::class, 'approve']);
    Route::post('/admin/activities/{id}/reject', [ActivityController::class, 'reject']);
    Route::delete('/admin/activities/{id}', [ActivityController::class, 'destroy'])->name('activities.destroy');
});

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

/*
|--------------------------------------------------------------------------
| Auth (Laravel Breeze)
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';