<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\Props\PropertiesController::class, 'index'])->name('home');
Route::get('/property-details/{id}', [App\Http\Controllers\Props\PropertiesController::class, 'single'])->name('single.property');
// Propiedades por tipo de inmueble: /properties, /properties/condo, etc.
Route::get('/properties', [App\Http\Controllers\Props\PropertiesController::class, 'all'])->name('properties.index');
Route::get('/properties/{homeType}', [App\Http\Controllers\Props\PropertiesController::class, 'byHomeType'])
    ->name('properties.byHomeType');
// Propiedades por oferta: /buy (sale) y /rent (rent/lease)
Route::get('/{type}', [App\Http\Controllers\Props\PropertiesController::class, 'byType'])
    ->where('type', 'buy|rent')
    ->name('properties.byType');
// Formulario de solicitud de información: POST desde single_property.blade.php -> RequestsController@insertRequest
Route::post('/requests', [App\Http\Controllers\Props\RequestsController::class, 'insertRequest'])->name('insert.request');

// Guardar/quitar propiedad de favoritos (toggle). Requiere login. POST desde icono corazón en home/blade.
Route::post('/save-property', [App\Http\Controllers\Props\PropertiesController::class, 'saveProperty'])->name('save.property')->middleware('auth');
