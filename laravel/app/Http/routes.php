<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

session_start();

    Route::get("/", array(
        'uses' => 'SoundController@index'
    ));
Route::get("/id/{id?}", array(
    'uses' => 'SoundController@index'
));

    Route::get("/getSongs/{emotion}/{intensity}/{offset?}", array(
        'uses' => 'SoundController@getSongs'
    ));

    Route::get("/getGenre/{genre}/{offset?}/{color?}{intensity?}", array(
        'uses' => "SoundController@getGenre"
    ));
Route::get("/getSimilar/{trackId}/{offset?}", array(
    'uses' => "SoundController@getSimilar"
));

Route::get("/getTrack/{trackId}", array(
    'uses' => "SoundController@getTrack"
));
Route::get("/getFeeling/{feeling}/{offset?}", array(
    'uses' => "SoundController@getFeeling"
));

Route::get("/saveSong", array(
    'uses' => "SoundController@saveSong"
));

Route::get("/showListening", array(
    'uses' => "SoundController@showListening"
));