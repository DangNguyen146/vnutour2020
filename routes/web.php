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

// Route::get('/', function(){
//     return view("home");
// });
Route::get('/', "RankingController@index");
Route::get('/minigame', "RankingController@index");
Route::get('/result', "ResultController@index");

// Route::get('/result/connection', "ResultController@connection");
Route::get('/result/{id}', "ResultController@showResult");

Route::get('/timeline', function(){
    return view("timeline");
});

Route::get('/lien-he', function(){
    return view("contact");
});