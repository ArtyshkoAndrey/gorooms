<?php

use App\Http\Controllers\RoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ImageController;

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


Route::match(['GET', 'POST'], '/search', 'SearchController')->name('search');
Route::match(['GET', 'POST'], '/search_map', 'SearchController')->name('search.map');

Route::get('/address/helper', 'Api\AddressController@helper');

Route::get('/hint', 'SearchController@hint')->name('hint');

Route::get('/room-info/{id}', [RoomController::class, 'getRoomInfo']);

Route::post('room/order/up/{id}', 'Lk\OrderRoomController@upOrder');
Route::post('room/order/down/{id}', 'Lk\OrderRoomController@downOrder');

Route::post('images/ordered', [ImageController::class, 'ordered']);