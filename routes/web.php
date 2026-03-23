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
| Dashboard
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [ActivityController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| USER (ผู้ใช้ทั่วไป)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:user'])->group(function () {

    Route::get('/activities', [ActivityController::class, 'index'])->name('activities');

    Route::get('/activities/{id}', [ActivityController::class, 'show'])->name('activities.show');

    Route::post('/activities/{id}/register', [RegistrationController::class,'register'])->name('activities.register');

    Route::get('/my-activities', [RegistrationController::class,'myActivities'])->name('My-activities');
});

/*
|--------------------------------------------------------------------------
| STAFF (คนสร้างกิจกรรม)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:staff'])->group(function () {

    Route::get('/create-activity', [ActivityController::class, 'create'])->name('activities.create');

    Route::post('/create-activity', [ActivityController::class, 'store'])->name('activities.store');

    Route::get('/my-created-activities', [ActivityController::class, 'myActivities']);
});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {

    // Registrations
    Route::get('/admin/registrations', [RegistrationController::class, 'adminIndex']);
    Route::post('/admin/registrations/{id}/approve', [RegistrationController::class, 'approve']);
    Route::post('/admin/registrations/{id}/reject', [RegistrationController::class, 'reject']);

    // Activities
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
| Auth
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';