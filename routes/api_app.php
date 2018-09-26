<?php

/**
 * mobi
 */

Route::post('test/post', 'Test\TestController@post');
Route::resource('test', 'Test\TestController');

//--------------------------------
Route::get('dash/count', 'Scope\SysDashController@count');

//----------------------------------
Route::post('auth/login', 'Auth\AuthController@loginApp');
Route::post('auth/logout', 'Auth\AuthController@logout');
Route::post('auth/checktoken', 'Auth\AuthController@checktoken');
Route::post('auth/userinfo', 'Auth\AuthController@userinfo');
Route::get('auth/captcha', 'Auth\AuthController@captcha');

Route::post('file/upload', 'Frame\FileController@upload');
Route::get('file/download', 'Frame\FileController@download');

Route::get('public/sys_dic', 'Data\PublicController@get_sys_dict');
Route::get('public/dict_dic', 'Data\PublicController@get_dict_dict');
Route::post('public/cantonTree', 'Data\PublicController@getCantonTree');

Route::post('ver/check', 'Data\VerController@CheckVersion');

Route::put('account/check_pwd/{id}', 'Data\SysAccountController@check_pwd');
Route::resource('account', 'Data\SysAccountController');

//角色
Route::get('role/dict', 'Data\SysRoleController@index_dict');
Route::post('menu/get_menu_list', 'Data\SysMenuController@get_menu_list');
Route::resource('logs', 'Data\SysLogsController');
Route::get('canton/selectselectselect/{id}', 'Data\CantonController@get_selectselectselect');
Route::get('canton/selectTree', 'Data\CantonController@getSelectTree');
Route::get('sysdic/tree/{id}', 'Data\SysDicController@tree');
Route::get('dictdic/tree/{id}', 'Data\DictDicController@tree');

//消息----------------------
Route::resource('message_info', 'Data\SysMessageInfoController');
