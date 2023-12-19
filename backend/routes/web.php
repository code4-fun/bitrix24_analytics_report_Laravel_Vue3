<?php

use App\Http\Controllers\Api\V1\Bitrix24Controller;
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
    return ['Laravel' => app()->version()];
});

//Route::get('/dashboard', [Bitrix24Controller::class, 'index']);

require __DIR__.'/auth.php';
