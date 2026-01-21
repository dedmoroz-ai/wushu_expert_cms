<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Scoreboard;
use App\Http\Controllers\CompetitionPdfController;
use App\Http\Controllers\ExportController;
use App\Models\Competition;

// Главная страница
Route::get('/', function () {
    // БЕРЕМ САМОЕ СВЕЖЕЕ СОРЕВНОВАНИЕ
    $activeCompetition = Competition::orderBy('start_date', 'desc')->first();

    return view('welcome', compact('activeCompetition'));
});

// Публичное табло
Route::get('/scoreboard', Scoreboard::class)->name('scoreboard');

// --- ГЕНЕРАЦИЯ PDF ПРОТОКОЛОВ ---
Route::get('/competition/{competition}/start-list', [CompetitionPdfController::class, 'startList'])
    ->name('competition.start-list');

Route::get('/competition/{competition}/final-results', [CompetitionPdfController::class, 'finalResults'])
    ->name('competition.final-results');

// --- ПЕЧАТЬ ДИПЛОМОВ ---
// Теперь принимаем ID соревнования, вида, возрастную группу и пол
Route::get('/competition/{competition}/diplomas/{style}/{age_group}/{gender}', [ExportController::class, 'downloadDiplomas'])
    ->name('export.diplomas');
