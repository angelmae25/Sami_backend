<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RegistrationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/status', function () {
    return response()->json([
        'success' => true,
        'application' => 'SAMI',
        'message' => 'SAMI API is running.',
    ]);
});

Route::prefix('v1')->group(function (): void {
    Route::post('/registrations', [RegistrationController::class, 'store'])
        ->middleware('throttle:6,1');
    Route::post('/registrations/{registrationId}/otp/verify', [RegistrationController::class, 'verify'])
        ->middleware('throttle:10,1');
    Route::post('/registrations/{registrationId}/otp/resend', [RegistrationController::class, 'resend'])
        ->middleware('throttle:2,1');
    Route::post('/registrations/{registrationId}/id-submission', [RegistrationController::class, 'submitId'])
        ->middleware('throttle:3,1');

    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:6,1');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/me', function (Request $request) {
            return response()->json([
                'user' => $request->user(),
            ]);
        });
    });
});
