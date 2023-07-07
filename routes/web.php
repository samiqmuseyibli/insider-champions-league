<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [\App\Http\Controllers\LeagueController::class, 'index'])->name('app');
Route::post('/league', [\App\Http\Controllers\LeagueController::class, 'store'])->name('league');
Route::get('/fixtures', [\App\Http\Controllers\FixtureController::class, 'fixtures'])->name('fixtures');
Route::put('/fixtures', [\App\Http\Controllers\FixtureController::class, 'changeFixture']);
Route::get('/fixtures/play', [\App\Http\Controllers\FixtureController::class, 'play'])->name('fixtures.play');
