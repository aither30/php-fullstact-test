<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MyClientController;

Route::get('/', function () {
    return view('welcome');
});
Route::resource('clients', MyClientController::class);