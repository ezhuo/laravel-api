<?php

Route::get('/', 'Test\IndexController@index');

Route::get('/error', 'Test\IndexController@err_404');
Route::post('file/upload/ckeditor', 'Frame\FileController@upload_ckeditor');