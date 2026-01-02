<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;

Route::get('/', [MovieController::class, 'index'])->name('movie.index');
Route::get('/search', [MovieController::class, 'search'])->name('movie.search');
Route::post('/send-email', [MovieController::class, 'sendEmail'])->name('movie.sendEmail');

