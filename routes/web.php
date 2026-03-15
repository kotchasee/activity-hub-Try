<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*
|---------------------------------------
| Dashboard
|---------------------------------------
*/

Route::get('/dashboard', [ActivityController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


/*
|---------------------------------------
| Activity
|---------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/activities', [ActivityController::class, 'index'])->name('activities');

    Route::get('/activities/{id}', [ActivityController::class, 'show']);

    Route::get('/create-activity', [ActivityController::class, 'create'])->name('activities.create');

    Route::post('/create-activity', [ActivityController::class, 'store'])->name('activities.store');

    // สมัครกิจกรรม
    Route::post('/activities/{id}/register', [RegistrationController::class,'register']);
   
    Route::get('/my-activities', [RegistrationController::class,'myActivities']);

    Route::post('/register-activity', [RegistrationController::class,'store'])->name('register.activity');

});


/*
|---------------------------------------
| Profile
|---------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

require __DIR__.'/auth.php';