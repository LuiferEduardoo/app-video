<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoController;

Route::get('/videos', [VideoController::class, 'all']);
Route::get('/videos/{id}', [VideoController::class, 'find']);
Route::get('/videos/{id}/stream', [VideoController::class, 'stream']);
Route::post('/videos', [VideoController::class, 'store']);
Route::put('/videos/{id}', [VideoController::class, 'update']);
Route::delete('/videos/{id}', [VideoController::class, 'destroy']);