<?php

use App\Http\Controllers\Api\V1\TodoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::name('v1.')
    ->prefix('v1')
    ->group(function () {
        Route::name('todos.')
            ->prefix('todos')
            ->controller(TodoController::class)
            ->group(function () {
                Route::post('/', 'store')->name('store');
            });
    });
