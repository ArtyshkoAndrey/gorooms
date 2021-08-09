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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::match(['GET', 'POST'], '/search', 'SearchController')->name('search');
Route::match(['GET', 'POST'], '/search_map', 'SearchController')->name('search.map');

//Route::get('get_coords', function (Request $request) {
//    try {
//        $city = $request->get('city');
//        $data = \Fomvasss\Dadata\Facades\DadataSuggest::suggest('address', ['query' => "г. $city", 'count' => 1]);
//    } catch (Exception $exception) {
//        return response()->json([
//            'success' => false,
//            'lat' => 0,
//            'lon' => 0,
//        ]);
//    }
//    return response()->json([
//        'lat' => $data['data']['geo_lat'],
//        'lon' => $data['data']['geo_lon'],
//    ]);
//});

Route::get('/address/helper', 'Api\AddressController@helper');