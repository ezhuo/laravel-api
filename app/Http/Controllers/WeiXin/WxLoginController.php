<?php

namespace App\Http\Controllers\WeiXin;

use Illuminate\Http\Request;
use App\Models\Auth\WxTokens;
use App\Models\WeiXin\WxUser;

class WxLoginController extends AppWxController {

    public function __construct(Request $request, WxUser $model) {
        parent::__construct($request, $model);
        $this->middleware('auth', ['except' => ['Login', 'UserInfo']]);
    }

    /**
     * 小程序登录获取用户信息
     * @param Request $request
     * @return mixed
     */
    public function Login(Request $request) {
        //code 在小程序端使用 wx.login 获取
        $code = $request['code'];
        //根据 code 获取用户 session_key 等信息, 返回用户openid 和 session_key
        if (!empty($code)) {
            $user = $this->getLoginInfo($code);
        }
        return return_json($user, ($user ? 'success' : 'error'), ($user ? HTTP_OK : HTTP_NOLOGIN));
    }

    public function UserInfo(Request $request) {
        if (!$request->has('loginData') && $request->has('userInfo')) {
            return return_json([], 'error', HTTP_WRONG);
        }
        // 保存用户
        $userInfo = $request->input('userInfo');
        $loginData = $request->input('loginData');
        $res = WxUser::write($request, $userInfo);
        if ($res) {
            $res = WxTokens::write($request, $loginData['session_key'], $loginData['openid'], $loginData['code'], $userInfo);
        }
        return return_json($res, ($res ? 'success' : 'error'), ($res ? HTTP_OK : HTTP_NOLOGIN));
    }

}
