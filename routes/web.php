<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Props\PropertiesController;
use App\Http\Controllers\Props\RequestsController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Admins\AdminController;
use App\Http\Controllers\Admins\AdminUserController;
use App\Http\Controllers\Admins\AdminPropertyController;
use App\Http\Controllers\Admins\AdminRequestController;
use App\Http\Controllers\Admins\AdminAgentApplicationController;
use App\Http\Controllers\Admins\AdminSectorController;
use App\Http\Controllers\Admins\HomeTypeController;
use App\Http\Controllers\Agent\AgentApplicationController;
use App\Http\Controllers\Agent\AgentDashboardController;
use App\Http\Controllers\Agent\AgentPropertyController;
use App\Http\Controllers\Agent\AgentRequestController;
use App\Http\Controllers\AmcExportController;
use App\Http\Controllers\AmcViewController;
use App\Http\Controllers\Api\AmcController as AmcApiController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Locale switcher
Route::get('/locale/{locale}', [LocaleController::class, 'setLocale'])->name('locale.switch');

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

// Agent application (apply & status) - requires auth, no agent role yet
Route::prefix('agent')->name('agent.')->group(function () {
    Route::get('/apply', [AgentApplicationController::class, 'showForm'])->name('apply');
    Route::post('/apply', [AgentApplicationController::class, 'store'])->name('apply.store')->middleware('auth');
    Route::get('/apply/status', [AgentApplicationController::class, 'status'])->name('apply.status')->middleware('auth');
});

// Agent dashboard (requires agent role)
Route::prefix('agent')->middleware(['auth', 'agent'])->name('agent.')->group(function () {
    Route::get('/dashboard', [AgentDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/properties', [AgentPropertyController::class, 'index'])->name('properties.index');
    Route::get('/properties/create', [AgentPropertyController::class, 'create'])->name('properties.create');
    Route::post('/properties', [AgentPropertyController::class, 'store'])->name('properties.store');
    Route::get('/properties/{property}/edit', [AgentPropertyController::class, 'edit'])->name('properties.edit');
    Route::put('/properties/{property}', [AgentPropertyController::class, 'update'])->name('properties.update');
    Route::patch('/properties/{property}/status', [AgentPropertyController::class, 'updateStatus'])->name('properties.updateStatus');
    Route::delete('/properties/{property}', [AgentPropertyController::class, 'destroy'])->name('properties.destroy');
    Route::get('/requests', [AgentRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/{propRequest}', [AgentRequestController::class, 'show'])->name('requests.show');
    Route::put('/requests/{propRequest}', [AgentRequestController::class, 'update'])->name('requests.update');
});

// Properties by offer type (buy/rent) - must be after specific routes
Route::get('/{type}', [PropertiesController::class, 'byType'])
    ->where('type', 'buy|rent')
    ->name('properties.byType');

// AMC (Análisis de Mercado Comparativo) - Agentes y Admins
Route::get('/amc', [AmcViewController::class, 'index'])
    ->middleware(['auth.agent_or_admin'])
    ->name('amc.index');
Route::post('/amc/run', [AmcApiController::class, 'run'])
    ->middleware(['auth.agent_or_admin'])
    ->name('amc.run');
Route::post('/amc/export-pdf', [AmcExportController::class, 'exportPdf'])
    ->middleware(['auth.agent_or_admin'])
    ->name('amc.export-pdf');

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

        // Sectores (AMC - exclusivo admin)
        Route::get('/sectores', [AdminSectorController::class, 'index'])->name('sectores.index');
        Route::get('/sectores/create', [AdminSectorController::class, 'create'])->name('sectores.create');
        Route::post('/sectores', [AdminSectorController::class, 'store'])->name('sectores.store');
        Route::get('/sectores/{sector}/edit', [AdminSectorController::class, 'edit'])->name('sectores.edit');
        Route::put('/sectores/{sector}', [AdminSectorController::class, 'update'])->name('sectores.update');
        Route::delete('/sectores/{sector}', [AdminSectorController::class, 'destroy'])->name('sectores.destroy');

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

        // Requests (solicitudes)
        Route::get('/requests', [AdminRequestController::class, 'index'])->name('requests.index');
        Route::get('/requests/{propRequest}', [AdminRequestController::class, 'show'])->name('requests.show');
        Route::put('/requests/{propRequest}', [AdminRequestController::class, 'update'])->name('requests.update');

        // Agent applications (solicitudes de agentes)
        Route::get('/agent-applications', [AdminAgentApplicationController::class, 'index'])->name('agent-applications.index');
        Route::get('/agent-applications/{agentApplication}', [AdminAgentApplicationController::class, 'show'])->name('agent-applications.show');
        Route::post('/agent-applications/{agentApplication}/approve', [AdminAgentApplicationController::class, 'approve'])->name('agent-applications.approve');
        Route::post('/agent-applications/{agentApplication}/reject', [AdminAgentApplicationController::class, 'reject'])->name('agent-applications.reject');
    });
});