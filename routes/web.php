<?php

use Illuminate\Support\Facades\Route;

// Redirect login/register to SPA root so the Laravel-served SPA mounts at '/'
Route::get('/login', function () {
    return view('dashboard');
})->name('login');

Route::get('/register', function () {
    return view('dashboard');
})->name('register');

// Protect the SPA catch-all with auth so authenticated users can access the app
Route::middleware(['auth'])->get('/{any}', function () {
    return view('dashboard');
})->where('any', '^(?!api).*$');

