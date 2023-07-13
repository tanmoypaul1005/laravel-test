<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\VisitorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'HomeIndex']);
Route::get('/visitor', [VisitorController::class, 'VisitorIndex']);
Route::get('/service', [ServicesController::class, 'ServiceIndex']);
Route::get('/get-service-data', [ServicesController::class, 'getServiceData']);
Route::get('/delete-services', [ServicesController::class, 'deleteServices']);
Route::get('/get-service-details', [ServicesController::class, 'getServiceDetails']);

Route::get('/home', function () {
    return view('home');
});

