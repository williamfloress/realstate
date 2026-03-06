<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Props\PropertiesController;
use App\Http\Controllers\Props\RequestsController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Admins\AdminController;
use App\Http\Controllers\Admins\AdminUserController;
use App\Http\Controllers\Admins\AdminPropertyController;
use App\Http\Controllers\Admins\HomeTypeController;
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


// Admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminController::class, 'login'])->name('login.submit');
    });
    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'adminDasboard'])->name('dashboard');
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

        // Admin users management
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');

        // Home types (tipos de propiedades)
        Route::get('/hometypes', [HomeTypeController::class, 'index'])->name('hometypes.index');
        Route::get('/hometypes/create', [HomeTypeController::class, 'create'])->name('hometypes.create');
        Route::post('/hometypes', [HomeTypeController::class, 'store'])->name('hometypes.store');

        // Properties (propiedades)
        Route::get('/properties', [AdminPropertyController::class, 'index'])->name('properties.index');
        Route::get('/properties/create', [AdminPropertyController::class, 'create'])->name('properties.create');
        Route::post('/properties', [AdminPropertyController::class, 'store'])->name('properties.store');
        Route::get('/properties/{property}/edit', [AdminPropertyController::class, 'edit'])->name('properties.edit');
        Route::put('/properties/{property}', [AdminPropertyController::class, 'update'])->name('properties.update');
        Route::patch('/properties/{property}/status', [AdminPropertyController::class, 'updateStatus'])->name('properties.updateStatus');
        Route::delete('/properties/{property}', [AdminPropertyController::class, 'destroy'])->name('properties.destroy');
    });
});