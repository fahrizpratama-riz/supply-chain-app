<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SupplyChainApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/countries', [SupplyChainApiController::class, 'getCountries']);
Route::get('/risk', [SupplyChainApiController::class, 'getRisk']);
Route::get('/ports', [SupplyChainApiController::class, 'getPorts']);
Route::get('/news', [SupplyChainApiController::class, 'getNews']);
Route::get('/currency', [SupplyChainApiController::class, 'getCurrency']);