<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
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

Route::middleware(['web'])->get('/check-auth', function () {
    return response()->json(['user' => Auth::guard('web')->user()]);
});

Route::get('/booking/success/{id}', function ($id) {
    return view('app');
})->name('booking-success');

Route::view("/{any}", "app")->where('any', '.*');

