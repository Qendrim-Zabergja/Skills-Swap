<?php

use App\Http\Controllers\SkillController;
use App\Http\Controllers\RequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['api'])->group(function () {
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/featured-skills', [SkillController::class, 'featured']);
    Route::get('/skills', [SkillController::class, 'getSkills']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/requests', [RequestController::class, 'store']);
    });
});