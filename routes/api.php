<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/drink/index', 'App\Http\Controllers\DrinkController@index')->name('drink/index');
Route::get('/drink/edit/{id}', 'App\Http\Controllers\DrinkController@editDrink')->name('drink/edit');
Route::post('/drink/commit/{name}/{mg_caffeine_per_serving}/{servings_per_container}', 'App\Http\Controllers\DrinkController@drinkAddCommit')->name('drink/commit');
Route::post('/drink/update/{id}/{name}/{mg_caffeine_per_serving}/{servings_per_container}', 'App\Http\Controllers\DrinkController@drinkUpdateCommit')->name('drink/update');
Route::post('/drink/delete/{id}', 'App\Http\Controllers\DrinkController@deleteDrink')->name('drink/delete');
Route::post('/drink/getDrinkLimitList/{drinkId}/{quantity}', 'App\Http\Controllers\DrinkController@drinkLimitList')->name('drink/getDrinkLimitList');
