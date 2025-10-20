<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExcelController;

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/', function () {
    return view('upload');
});

Route::post('/upload', [ExcelController::class, 'upload']);
Route::get('/rows', [ExcelController::class, 'getRows']);
Route::get('/progress', [ExcelController::class, 'getProgress']);

//Route::get('/redis', [App\Http\Controllers\Controller::class, 'redis']);
