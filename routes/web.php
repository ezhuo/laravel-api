<?php

Route::get('/', 'Test\IndexController@index');

Route::get('/error', 'Test\IndexController@err_404');

Route::get('export', 'Test\IndexController@export');