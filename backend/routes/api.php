<?php

use App\Http\Controllers\Api\PublicCustomerRequestController;
use Illuminate\Support\Facades\Route;

Route::prefix('public/v1')->middleware('throttle:public')->group(function (): void {
    Route::get('/request-template', [PublicCustomerRequestController::class, 'requestTemplate']);
    Route::get('/request-requirements', [PublicCustomerRequestController::class, 'requirements']);
    Route::post('/requests', [PublicCustomerRequestController::class, 'store']);
    Route::post('/tracking', [PublicCustomerRequestController::class, 'track']);
    Route::post('/tracking/notifications/read', [PublicCustomerRequestController::class, 'markNotificationRead']);
});
