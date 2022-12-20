<?php

use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\BookingController;
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

Route::get('bookings/{booking}/invoice', [BookingController::class, 'invoice'])
    ->name('get.bookings.single.invoice');

Route::get('bookings/{booking}/download', [BookingController::class, 'download'])
    ->name('get.bookings.single.download');

Route::post('apartments/{apartment}/available', [ApartmentController::class, 'available'])
    ->name('post.apartments.single.available');
