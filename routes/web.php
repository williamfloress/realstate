<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Props\PropertiesController;
use App\Http\Controllers\Props\RequestsController;
use App\Http\Controllers\Users\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', [PropertiesController::class, 'index'])->name('home');
Route::get('/home', [PropertiesController::class, 'index'])->name('home');

Auth::routes();

// Properties
Route::prefix('properties')->group(function () {
    Route::get('/', [PropertiesController::class, 'all'])->name('properties.index');
    Route::get('/price-asc', [PropertiesController::class, 'priceAsc'])->name('price.asc.properties');
    Route::get('/price-desc', [PropertiesController::class, 'priceDesc'])->name('price.desc.properties');
    Route::get('/{homeType}', [PropertiesController::class, 'byHomeType'])->name('properties.byHomeType');
});

Route::get('/property-details/{id}', [PropertiesController::class, 'single'])->name('single.property');

// Static pages
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// User (authenticated)
Route::prefix('user')->middleware('auth')->name('user.')->group(function () {
    Route::get('/requests', [UserController::class, 'myRequests'])->name('requests');
    Route::get('/favorites', [UserController::class, 'myFavorites'])->name('favorites');
});

// Properties by offer type (buy/rent) - must be after specific routes
Route::get('/{type}', [PropertiesController::class, 'byType'])
    ->where('type', 'buy|rent')
    ->name('properties.byType');

// POST actions
Route::post('/requests', [RequestsController::class, 'insertRequest'])->name('insert.request');
Route::post('/save-property', [PropertiesController::class, 'saveProperty'])
    ->name('save.property')
    ->middleware('auth');
