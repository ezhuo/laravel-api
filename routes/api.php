<?php

Route::get('/', 'Test\IndexController@index');
Route::get('/userStatusQuery', 'Test\IndexController@userStatusQuery');

Route::get('/error', 'Test\IndexController@err_404');
Route::post('file/upload/ckeditor', 'Frame\FileController@upload_ckeditor');