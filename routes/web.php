<?php

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

Route::get('/', function() {
    $resource = \App\Models\Resource::all();
    return view('welcome', compact('resource'));
});

Route::get('/admin', function () {
    $resource = \App\Models\Resource::all();
    return view('resource', compact('resource'));
});
