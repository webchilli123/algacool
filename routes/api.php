<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LeadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json(['message' => 'API working']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// login
Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
// Route::prefix('v1')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // Dashboard
    // Route::get('dashboard', [DashboardController::class, 'index']);

    // leads
    Route::prefix('lead')->group(function () {
        Route::get('create', [LeadController::class, 'create']);
        Route::get('index', [LeadController::class, 'index']);
        Route::post('store', [LeadController::class, 'store']);
        Route::get('edit/{id}', [LeadController::class, 'edit']);
        Route::put('update/{id}', [LeadController::class, 'update']);
        Route::delete('delete/{id}', [LeadController::class, 'destroy']);
        Route::put('update-followup/{id}', [LeadController::class, 'updateFollowupApi']);
    });

    // // complaint
    // Route::prefix('complaint')->group(function () {
    //     Route::get('create', [ComplaintController::class, 'create']);
    //     Route::get('index', [ComplaintController::class, 'index']);
    //     Route::post('store', [ComplaintController::class, 'store']);
    //     Route::get('edit/{id}', [ComplaintController::class, 'edit']);
    //     Route::put('update/{id}', [ComplaintController::class, 'update']);
    //     Route::delete('delete/{id}', [ComplaintController::class, 'destroy']);
    //     Route::post('start-work', [ComplaintController::class, 'startWork']);
    //     Route::post('finished-work', [ComplaintController::class, 'finishWork']);
    // });

    //  // quotation
    // Route::prefix('quotation')->group(function () {
    //     Route::get('create', [QuotationController::class, 'create']);
    //     Route::get('index', [QuotationController::class, 'index']);
    //     Route::post('store', [QuotationController::class, 'store']);
    //     Route::get('edit/{id}', [QuotationController::class, 'edit']);
    //     Route::put('update/{id}', [QuotationController::class, 'update']);
    //     Route::delete('delete/{id}', [QuotationController::class, 'destroy']);
    // });

    // Route::get('/get-party-details/{id}', [ComplaintController::class, 'getPartyDetails']);
    // Route::get('/stock', [ReportController::class, 'stock']);
    
});
