<?php

/**
 * weixin
 */
Route::post('decode', 'WeiXin\WxPublicController@decode');

Route::get('login', 'WeiXin\WxLoginController@Login');
Route::post('userInfo', 'WeiXin\WxLoginController@UserInfo');

Route::get('bookmall/tags', 'WeiXin\WxTestController@index1');
Route::post('bookmall/list', 'WeiXin\WxTestController@index2');