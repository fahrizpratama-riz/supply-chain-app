<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SupplyChainApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/* ============================================================
   CORE DATA ENDPOINTS
   ============================================================ */
Route::get('/countries', [SupplyChainApiController::class, 'getCountries']);
Route::get('/risk',      [SupplyChainApiController::class, 'getRisk']);
Route::get('/ports',     [SupplyChainApiController::class, 'getPorts']);
Route::get('/news',      [SupplyChainApiController::class, 'getNews']);
Route::get('/currency',  [SupplyChainApiController::class, 'getCurrency']);

/* ============================================================
   ANALYTICS ENDPOINTS
   ============================================================ */
Route::get('/compare',          [SupplyChainApiController::class, 'getCompare']);
Route::get('/gdp-trend',        [SupplyChainApiController::class, 'getGdpTrend']);
Route::get('/inflation-trend',  [SupplyChainApiController::class, 'getInflationTrend']);

/* ============================================================
   WATCHLIST ENDPOINTS
   ============================================================ */
Route::get('/watchlist',          [SupplyChainApiController::class, 'getWatchlist']);
Route::post('/watchlist',         [SupplyChainApiController::class, 'addWatchlist']);
Route::delete('/watchlist/{iso}', [SupplyChainApiController::class, 'removeWatchlist']);

/* ============================================================
   ARTICLES ENDPOINTS
   ============================================================ */
Route::get('/articles',        [SupplyChainApiController::class, 'getArticles']);
Route::post('/articles',       [SupplyChainApiController::class, 'storeArticle']);
Route::delete('/articles/{id}',[SupplyChainApiController::class, 'deleteArticle']);

/* ============================================================
   ADMIN ENDPOINTS
   ============================================================ */
Route::prefix('admin')->group(function () {
    Route::get('/users',   [SupplyChainApiController::class, 'getAdminUsers']);
    Route::get('/stats',   [SupplyChainApiController::class, 'getAdminStats']);
});

/* ============================================================
   SYSTEM ENDPOINTS
   ============================================================ */
Route::get('/alert-logs',    [SupplyChainApiController::class, 'getAlertLogs']);
Route::get('/risk-weights',  [SupplyChainApiController::class, 'getRiskWeights']);