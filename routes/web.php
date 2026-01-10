<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home page
Route::get('/', function () {
    return view('welcome');
});

// AI Chatbot page
Route::get('/chatbot', function () {
    return view('chatbot');
});

// Admin panel routes (handled by Filament)
// Access at: /admin

// Installation routes (handled by installer)
// Access at: /install
