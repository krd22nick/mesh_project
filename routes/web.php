<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/redis', [App\Http\Controllers\Controller::class, 'redis']);
