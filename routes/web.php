<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;

Route::get('/', [MainController::class, 'index'])->name('football.index');
Route::get('/times', [MainController::class, 'times'])->name('football.times');
Route::get('/times/detalhes/{id}/{name}', [MainController::class, 'detalhes'])->name('football.detalhes.times');

Route::get('/teams-by-league', [MainController::class, 'getTeamsByLeague'])->name('getTeamsByLeague');
Route::get('/latest-results', [MainController::class, 'getLatestMatchesByLeague'])->name('getLatestMatchesByLeague');
Route::get('/next-matches', [MainController::class, 'getNextMatchesByLeague'])->name('getNextMatchesByLeague');
Route::get('/leagues', [MainController::class, 'getLeagues'])->name('getLeagues');