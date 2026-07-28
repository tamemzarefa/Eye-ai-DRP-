<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return view('dashboard');
})->name('login');

Route::get('/register', function () {
    return view('dashboard');
})->name('register');

Route::get('/{any}', function () {
    return view('dashboard');
})->where('any', '^(?!api).*$');

