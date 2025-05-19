<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReminderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    Route::apiResource('clients', ClientController::class);

    Route::post('/appointments', [AppointmentController::class, 'store'])->name('api.appointments.store');

    Route::get('/appointments/upcoming', [AppointmentController::class, 'upcoming'])->name('api.appointments.upcoming');
    Route::get('/appointments/past', [AppointmentController::class, 'past'])->name('api.appointments.past');


    Route::get('/reminders/scheduled', [ReminderController::class, 'scheduled'])->name('api.reminders.scheduled');
    Route::get('/reminders/sent', [ReminderController::class, 'sent'])->name('api.reminders.sent');

    Route::post('/reminders/{reminder}/toggle-channel', [ReminderController::class, 'toggleChannel'])
        ->name('api.reminders.toggle-channel');
});



