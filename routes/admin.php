<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\Admin\InstructionController;

/*
|--------------------------------------------------------------------------
| Admin routes
|--------------------------------------------------------------------------
*/
Route::get('/', 'HomeController@index')->name('index');

Route::resource('hotels', 'HotelController');
Route::resource('rooms', 'RoomController', [
  'except' => ['create'],
]);

Route::get('/rooms/create/{hotel}', 'RoomController@create')->name('rooms.create');
Route::get('/rooms/block/{hotel}', 'HotelController@block')->name('hotels.block');
Route::get('/rooms/unblock/{hotel}', 'HotelController@unblock')->name('hotels.unblock');

Route::get('/attributes/{category}', 'AttributeController@index')
  ->name('attributes.index')
  ->where('category', '(room|hotel)');
Route::resource('attributes', 'AttributeController', [
  'except' => ['index'],
]);
Route::resource('attributes_categories', 'AttributeCategoryController');

Route::resource('hotels/{hotel?}/categories', 'CategoryController', [
  'except' => ['index'],
]);
Route::resource('cost_types', 'CostTypeController', [
  'except' => ['show'],
]);
Route::resource('hotel_types', 'HotelTypeController', [
  'except' => ['show'],
]);
Route::resource('ratings', 'RatingCategoryController', [
  'except' => ['show'],
]);

Route::resource('hotels/{hotel}/reviews', 'ReviewController');
Route::resource('pages', 'PageController', [
  'except' => ['show'],
]);
Route::resource('articles', 'ArticleController', [
  'except' => ['show'],
]);

Route::get('settings', 'SettingsController@index')->name('settings.index');
Route::get('settings/seo', 'SettingsController@seo')->name('settings.seo');
Route::get('settings/seo/edit/{id}', 'SettingsController@seoEdit')->name('settings.seo.edit');
Route::put('settings/seo/update/{id}', 'SettingsController@seoUpdate')->name('settings.seo.update');
Route::post('settings', 'SettingsController@store')->name('settings.store');
Route::post('settings/robot', 'SettingsController@storeRobot')->name('settings.robot_store');

Route::resource('forms', 'FormController', [
  'only' => ['index', 'show'],
]);

Route::resource('descriptions', 'PageDescriptionController', [
  'except' => 'show',
]);

Route::group(['prefix' => 'api'], function () {
  Route::delete('/image/{image}', 'ImageController@delete')->name('api.image.delete');
});

Route::match(['GET', 'POST'], '/upload', 'ImageController@upload');
Route::match(['GET', 'POST'], '/upload_for/', 'ImageController@uploadFor');

Route::get('/clear-cache', 'SettingsController@clearCache')->name('clear-cache');

Route::post('/periods/updateByJson', [PeriodController::class, 'updateByJson'])->name('periods.update.json');

Route::resource('moderators', 'ModeratorController');

Route::resource('instructions', 'InstructionController');