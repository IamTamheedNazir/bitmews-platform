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

// Admin panel routes (handled by Filament)
// Access at: /admin

// Installation routes (handled by installer)
// Access at: /install
