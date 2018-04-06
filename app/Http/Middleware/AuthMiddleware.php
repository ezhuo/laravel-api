<?php

namespace App\Http\Middleware;

use Closure;
use Log;
use Illuminate\Support\Facades\DB;
use App\Models\Auth\Tokens;
use App\Models\Auth\WxTokens;
use App\Models\WeiXin\WxUser;

class AuthMiddleware extends BaseMiddleware {

    /**
     * 验证权限
     * @param $request
     * @param Closure $next
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, Closure $next) {
        Log::info('auth');
        $user = null;
        $token = $request->header('token');
        $message = HTTP_NOLOGIN_MESSAGE;
        //如果是导出的话
        if (empty($token) && check_url_auth_list($request)) {
            $token = $request->route()->__get('token');
        }
        if (!empty($token)) {
            if ($request->__monitor == 'weixin') {
                // 小程序客户端
                $message = HTTP_WEIXIN_NOLOGIN_MESSAGE;
                $user = $this->get_weixin_user($request, $token);
            } else {
                $user = $this->get_tokens_info($request, $token);
            }
            $request->__user = $user;
        }
//        dd($user);
        if (empty($user) && AUTH_ENABLED) {
            return return_json([], $message, HTTP_NOLOGIN);
        } else {
            return $next($request);
        }
    }

    protected function get_tokens_info($request, $token) {
        $tokens = Tokens::read($token);
        $user = null;
        if ($tokens) {
            $user = token_decode($tokens['token_info']);
        }
        return $user;
    }

    /**
     * 获取监管用户
     * @param $request
     */
    protected function get_sys_account($request, $token) {
        return [];
    }

    /**
     * 获取评估机构的用户
     * @param $request
     */
    protected function get_org_account($request, $token) {
        return [];
    }

    protected function get_weixin_user($request, $token) {
        $tokens = WxTokens::read($token);
        $user = null;
        if ($tokens) {
            $openid = $tokens->openid;
            $user = WxUser::find($openid);
        }
        return $user;
    }
}
