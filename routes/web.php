<?php

use App\Http\Controllers\AuthenticateController;
use App\Http\Controllers\HomeController;
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

Route::get('/', [HomeController::class, 'homePage'])->name('home');

Route::get('/authorize/{update_token}', [HomeController::class, 'authorizePage'])->name('authorize');

Route::get('/authenticate/complete', [AuthenticateController::class, 'authenticateCallback'])->name('authenticate.callback');
Route::get('/authenticate/{update_token}', [AuthenticateController::class, 'authenticatePage'])->name('authenticate');

Route::get('/success', function (){
    return view('pages.success');
});
Route::get('/error', function (){
    return view('pages.error');
});
