<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SwapRequestController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\BrowseController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('home');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Browse routes
    Route::get('/browse', [BrowseController::class, 'index'])->name('browse.index');
    
    // Skills routes
    Route::get('/skills', [SkillController::class, 'index'])->name('skills.index');
    Route::get('/skills/browse', [SkillController::class, 'browse'])->name('skills.browse');
    Route::get('/skills/{skill}', [SkillController::class, 'show'])->name('skills.show');
    Route::post('/skills', [SkillController::class, 'store'])->name('skills.store');
    Route::put('/skills/{skill}', [SkillController::class, 'update'])->name('skills.update');
    Route::delete('/skills/{skill}', [SkillController::class, 'destroy'])->name('skills.destroy');
    
    // Swap requests routes
    Route::get('/requests', [SwapRequestController::class, 'index'])->name('requests.index');
    Route::post('/requests', [SwapRequestController::class, 'store'])->name('requests.store');
    Route::post('/requests/{swapRequest}/accept', [SwapRequestController::class, 'accept'])->name('requests.accept');

    // Rating routes
    Route::post('/requests/{skillRequest}/rate', [RatingController::class, 'store'])->name('ratings.store');
    Route::get('/requests/{skillRequest}/rating-status', [RatingController::class, 'checkStatus'])->name('ratings.check-status');
    Route::get('/users/{user}/ratings', [RatingController::class, 'userRatings'])->name('ratings.user');
    Route::post('/requests/{swapRequest}/decline', [SwapRequestController::class, 'decline'])->name('requests.decline');
    Route::post('/requests/{swapRequest}/cancel', [SwapRequestController::class, 'cancel'])->name('requests.cancel');
    
    // Messages routes
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{user}', [MessageController::class, 'store'])->name('messages.store');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';