<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;



Route::get('/', function () {
    return view('welcome');
});

//Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get(
    '/mtn',
    function () {
        return view('mtn');
    }
);

Route::get('/payment', [AuthController::class, 'emailpromot'])->name('payment');
Route::get('/code', [AuthController::class, 'mtn'])->name('code');