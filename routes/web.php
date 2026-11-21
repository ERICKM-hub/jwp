<?php

use App\Http\Controllers\ContactInquiryController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/team', [PageController::class, 'team'])->name('team');
Route::get('/insights', [PageController::class, 'insights'])->name('insights');
Route::get('/insights/{slug}', [PageController::class, 'insightShow'])->name('insights.show');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [ContactInquiryController::class, 'store'])
    ->middleware('throttle:contact')
    ->name('contact.submit');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacy');
