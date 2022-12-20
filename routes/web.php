<?php

use App\Http\Controllers\RentalController;
use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Route::get('test', function () {
    return 'Hello World';
});

Route::get('rentals/{rental}/invoice', [RentalController::class, 'invoice'])
    ->name('get.rentals.single.invoice');

Route::get('rentals/{rental}/download', [RentalController::class, 'download'])
    ->name('get.rentals.single.download');
