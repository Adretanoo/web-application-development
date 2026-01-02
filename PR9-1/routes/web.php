<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;

Route::get('/', [WeatherController::class, 'index'])->name('weather.index');

Route::get('/today', [WeatherController::class, 'today'])->name('weather.today');
Route::get('/week', [WeatherController::class, 'week'])->name('weather.week');
