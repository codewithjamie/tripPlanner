<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DestinationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('search');
});
Route::get('/map', function () {
    return view('map');
});
Route::get('/destination-routing', [DestinationController::class, 'index'])->name('destination');


Route::post('/api/destinations', [DestinationController::class, 'store']);
Route::post('/api/route-details', [DestinationController::class, 'getRouteDetails']);
