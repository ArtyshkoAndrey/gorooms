<?php
/*
 * Copyright (c) 2021.
 * This code is the property of the Fulliton developer.
 * Write all questions and suggestions on the Vkontakte social network https://vk.com/fulliton
 */

Route::get('/hotel/{id}', 'ObjectController@edit')->name('object.edit');
Route::post('/hotel/{id}', 'ObjectController@update')->name('object.update');

Route::post('/hotel/{id}/upload', 'ObjectController@upload')->name('object.upload');
Route::post('/hotel/{id}/unupload', 'ObjectController@unupload')->name('object.unupload');

Route::delete('/image/{image}', 'ImageController@delete')->name('image.delete');
Route::post('/image/moderate/{image}', 'ImageController@moderate')->name('image.moderate');

Route::get('/hotel/{id}/rooms', 'RoomController@edit')->name('room.edit');
Route::post('/room/update', 'RoomController@update')->name('room.update');

Route::delete('room/delete/{id}', 'RoomController@delete')->name('room.delete');

Route::put('category/update', 'CategoryController@update')
  ->name('category.update');
Route::post('category/create', 'CategoryController@create')
  ->name('category.create');
Route::delete('category/delete/{category}', 'CategoryController@delete')
  ->name('category.delete');

Route::get('room/attrs/{id}', 'RoomController@getAttributes')->name('room.attr.get');
Route::put('room/attrs/{id}', 'RoomController@putAttributes')->name('room.attr.put');
Route::post('room/published/{id}', 'RoomController@published')->name('room.published');

Route::get('instruction/{id}', 'InstructionController@index')->name('instruction.index');

Route::post('staff/create-user/{id}', 'StaffController@create')->name('staff.create');
Route::get('hotel/{id}/staff', 'StaffController@index')->name('staff.index');
Route::delete('staff/remove/{staff_id}', 'StaffController@remove')->name('staff.remove');
Route::put('staff/update/{staff_id}', 'StaffController@update')->name('staff.update');
Route::post('staff/update/{staff_id}/password', 'StaffController@generatePassword')->name('staff.update.password');
