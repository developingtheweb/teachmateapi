<?php

use App\Http\Controllers\YoutubeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/youtube', [YoutubeController::class, 'getTranscript'])->middleware('auth:sanctum');
