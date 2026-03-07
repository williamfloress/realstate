<?php

use App\Http\Controllers\Api\AmcController;
use Illuminate\Support\Facades\Route;

Route::post('amc/run', [AmcController::class, 'run']);
