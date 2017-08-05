<?php

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

Route::get('discord', function () {
    return redirect()->to(config('discord.server'));
})->name('discord-invite');

Route::get('login', 'UserController@login');
Route::get('logout', 'UserController@logout');

Route::get('dashboard', function () {
    return 'There are nothing here right now.';
});