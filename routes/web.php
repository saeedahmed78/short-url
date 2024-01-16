<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UrlController;

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


// Route::get('/', [UrlController::class, 'index'])->name('url.index');

Route::get('/', function () {
    return view('index');
})
->name('app');



Route::get('/', [UrlController::class, 'index'])->name('url.index');
Route::post('/store', [UrlController::class, 'store'])->name('url.store');
Route::get('/shortly/{hash}', [UrlController::class, 'redirect'])->name('url.redirect');

