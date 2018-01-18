<?php

namespace App\Http\Controllers\Auth;

use App\Models\Data\SysLogs;
use Illuminate\Http\Request;
use App\Models\Auth\Auth;
use App\Models\Auth\Tokens;
use App\Models\Data\SysMenu;
use App\Http\Controllers\Frame\AppDataController;

class AuthController extends AppDataController {
    public function __construct(Request $request, Auth $model) {
        parent::__construct($request, $model);

        $this->middleware('auth', ['except' => ['login_pc', 'login_mobi_org']]);
    }

    public function checktoken(Request $request) {
        $token = $request['token'];
        $res = Tokens::read($token);
        if ($res) {
            return return_json([], '身份验证通过！');
        } else {
            return return_json([], '身份验证失败', HTTP_NOAUTH);
        }
    }

    public function userinfo(Request $request) {
        $res = token_decode($request->header('token'));
        if ($res) {
            return return_json($res);
        } else {
            return return_json([], '获取身份信息失败！', HTTP_NOAUTH);
        }
    }

    // --------------------------------

    protected function user_login($request, $login_type) {
        if ($login_type == 'sys') {
            $res = Auth::login_pc_sys($request);
        } else if ($login_type == 'org') {
            $res = Auth::login_pc_org($request);
        }

        if ($res['code'] == 201) {
            return return_json([], '请输入帐号和密码！', HTTP_NOAUTH);
        }
        if ($res['code'] == 202) {
            return return_json([], '帐号和密码无效！', HTTP_NOAUTH);
        }
	if ($res['code'] == 203) {
            return return_json([], '帐号因未验证、已禁用或该员工已离职等原因，故而登录失败！', HTTP_NOAUTH);
        }
        if ($res['code'] != 200) {
            return return_json([], '登录失败！', HTTP_NOAUTH);
        }

        //帐号和密码验证成功
        $loginInfo = "";
        $tk = Tokens::read_auth_id($request, $res['account']->id);
        if (!empty($tk)) {
//            Tokens::destroy_auth_id($request, $res['account']->id);
//            $loginInfo = "提醒：该帐号已在其它设备上登录，其它设备登录的帐号将会自动退出。";
        }

        $list['token'] = token_encode($res['account']);
        $sys_menu = new SysMenu();
        $list['menu_list'] = $sys_menu->get_menu($res['account']);
        Tokens::write($request, $list['token'], $res['account']->id);

        $request->__user = $res['account'];
        SysLogs::write($request, '登录');
        return return_json($list, $loginInfo);
    }

    public function login_pc(Request $request) {
        $request->__source = $request['login_type'];
        return $this->user_login($request, $request['login_type']);
    }

    public function login_mobi_org(Request $request) {
        return $this->user_login($request, 'org');
    }

    public function logout(Request $request) {
        $res = Tokens::destroy($request->header('token'));
        if ($res != false) {
            $request->__source = $request->__user->style;
            SysLogs::write($request, '登出');
            return return_json([], '注销成功...');
        } else {
            return return_json([], '注销失败', HTTP_NOAUTH);
        }
    }
}
