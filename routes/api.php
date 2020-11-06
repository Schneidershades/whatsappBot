<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/chat-bot', 'Chat\ChatBotController@listenToReplies');
Route::resource('/brands', 'Api\BrandController');
Route::resource('/components', 'Api\ComponentController');
Route::resource('/category', 'Api\CategoryController');
Route::resource('/models', 'Api\CarModelController');
Route::resource('/make', 'Api\MakeController');
Route::resource('/year', 'Api\YearController');
Route::resource('/search', 'Api\SearchController');

