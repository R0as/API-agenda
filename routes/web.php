<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/agenda', [\App\Http\Controllers\CalendarController::class, 'index']);

Route::get('/admin', function () {
    return view('admin.users');
});
