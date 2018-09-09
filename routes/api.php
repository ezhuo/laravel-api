<?php

Route::get('/', 'Test\IndexController@index');

Route::get('/error', 'Test\IndexController@err_404');
Route::post('file/upload/ckeditor', 'Frame\FileController@upload_ckeditor');

Route::get('test/captcha', 'Test\IndexController@captcha');
Route::get('test/check', 'Test\IndexController@check');
Route::get('test/captcha2', 'Test\IndexController@captcha2');
Route::get('/userStatusQuery', 'Test\IndexController@userStatusQuery');