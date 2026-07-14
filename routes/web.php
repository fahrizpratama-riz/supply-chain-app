<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard.index');
});

Route::get('/countries', function () {
    return view('dashboard.countries');
});

Route::get('/weather', function () {
    return view('dashboard.weather');
});

Route::get('/ports', function () {
    return view('dashboard.ports');
});

Route::get('/news', function () {
    return view('dashboard.news');
});

Route::get('/settings', function () {
    return view('dashboard.settings');
});