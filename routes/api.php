<?php


/*
|------------------------------------------------------
|   API Routes
|------------------------------------------------------
*/

use App\Http\Controllers\StatsController;
use App\Http\Controllers\TicketController;

Route::get('/tickets', [TicketController::class, 'index']);  // list with filters
Route::post('/tickets', [TicketController::class, 'store']);  // saving a ticket
Route::get('/tickets/{id}', [TicketController::class, 'show']); // show a single ticket
Route::patch('/tickets/{id}', [TicketController::class, 'update']); //update a ticket status / category / note
Route::post('/tickets/{id}/classify', [TicketController::class, 'classify']);  // trigger classification job

Route::get('/stats', [StatsController::class, 'index']); //dashboard counters