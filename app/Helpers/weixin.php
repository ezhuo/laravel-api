<?php

/**
 * 小程序APPID
 */
const weixin_appid = 'wx460e69760c38061f';

/**
 * 小程序Secret
 */
const weixin_secret = 'e2e1eaa4ab9468759199f33c03aa7304';

/**
 * 小程序登录凭证 code 获取 session_key 和 openid 地址，不需要改动
 */
const weixin_code2session_url = "https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code";