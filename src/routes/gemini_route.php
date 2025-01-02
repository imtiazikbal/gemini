<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/view',[App\Http\Controllers\GeminiController::class, 'view']);
Route::get('/getUserDocumentsResponses',[App\Http\Controllers\GeminiController::class, 'documentsResponses']);

Route::post('/documents/summarize',action: [App\Http\Controllers\GeminiController::class, 'summarizeSingleDocument'])->name('documents');
