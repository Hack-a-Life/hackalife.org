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

Route::get('login', 'UserController@login')->name('login');
Route::get('logout', 'UserController@logout');

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'discord']], function () {
    Route::get('/', function () {
        return 'There is nothing here yet, click <a href="/logout">here to logout.</a>';
    });
});