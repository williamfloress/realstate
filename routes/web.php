<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\Props\PropertiesController::class, 'index'])->name('home');
Route::get('/property-details/{id}', [App\Http\Controllers\Props\PropertiesController::class, 'single'])->name('single.property');
Route::post('/requests', [App\Http\Controllers\Props\RequestsController::class, 'insertRequest'])->name('insert.request');
