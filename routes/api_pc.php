<?php

/**
 * pc
 */

Route::post('test/post', 'Test\TestController@post');
Route::resource('test', 'Test\TestController');

//--------------------------------
Route::get('dash/count', 'Scope\SysDashController@count');

//----------------------------------
Route::post('auth/login', 'Auth\AuthController@login_pc');
Route::post('auth/logout', 'Auth\AuthController@logout');
Route::post('auth/checktoken', 'Auth\AuthController@checktoken');
Route::post('auth/userinfo', 'Auth\AuthController@userinfo');
Route::get('auth/captcha', 'Auth\AuthController@captcha');

Route::post('file/upload', 'Frame\FileController@upload');
Route::get('file/download', 'Frame\FileController@download');

Route::post('public/cache_clear', 'Data\PublicController@cache_clear');
Route::get('public/sys_dic', 'Data\PublicController@get_sys_dict');
Route::get('public/dict_dic', 'Data\PublicController@get_dict_dict');
Route::post('public/cantonTree', 'Data\PublicController@getCantonTree');

Route::put('account/check_pwd/{id}', 'Data\SysAccountController@check_pwd');
Route::resource('account', 'Data\SysAccountController');

//角色
Route::get('role/dict', 'Data\SysRoleController@index_dict');
Route::resource('role', 'Data\SysRoleController');

Route::post('menu/get_menu_list', 'Data\SysMenuController@get_menu_list');
Route::resource('menu', 'Data\SysMenuController');
Route::resource('logs', 'Data\SysLogsController');
Route::resource('loglogin', 'Data\SysLoginLogsController');
Route::resource('syssetting', 'Data\SysSettingController');

Route::get('canton/selectselectselect/{id}', 'Data\CantonController@get_selectselectselect');
Route::get('canton/selectTree', 'Data\CantonController@getSelectTree');
Route::resource('canton', 'Data\CantonController');

Route::get('sysdic/tree/{id}', 'Data\SysDicController@tree');
Route::resource('sysdic', 'Data\SysDicController');

Route::get('dictdic/tree/{id}', 'Data\DictDicController@tree');
Route::resource('dictdic', 'Data\DictDicController');

// 机构
// id=当前导出的ID,TOKEN用户身份,chk验证
Route::get('orginfo/dict', 'Scope\OrgInfoController@index_dict');
Route::get('orginfo/tree', 'Scope\OrgInfoController@index_tree');
Route::get('orginfo/exports/{id}/{style}/{token}/{validate}', 'Scope\OrgInfoController@exports');
Route::resource('orginfo', 'Scope\OrgInfoController');


//消息----------------------
Route::resource('message_info', 'Data\SysMessageInfoController');
