<?php
/*
 * Copyright (c) 2021.
 * This code is the property of the Fulliton developer.
 * Write all questions and suggestions on the Vkontakte social network https://vk.com/fulliton
 */

Route::get('/hotel/{id}', 'ObjectController@edit')->name('object.edit');
Route::post('/hotel/{id}', 'ObjectController@update')->name('object.update');

Route::delete('/image/{image}', 'ImageController@delete')->name('image.delete');
Route::post('/image/moderate/{image}', 'ImageController@moderate')->name('image.moderate');

