<?php

namespace App\Http\Middleware;

use Closure;
use Log;
use Illuminate\Routing\Route;

class BeforeMiddleware {

    /**
     * 验证请求包，是否合法
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
//        dd(get_uuid());
        Log::info("[" . $request->method() . "]" . $request->url() . "\r\n" . print_r($request->all(), true));
        $res = $this->request_validate($request);
        if ($res === false && AUTH_ENABLED) {
            return return_json([], HTTP_NOTACCEPT_MESSAGE, HTTP_NOTACCEPT);
        } else {
            return $next($request);
        }
    }

    //验证签名
    protected function request_validate($request) {
        if (check_no_auth_list($request)) {
            return true;
        }

        if (check_url_auth_list($request)) {
            //要是不重header里发，必须要在URL里发送
            $route = $request->route();
            $token = $route->__get('token');
            $style = $route->__get('style');
            $md5_client = $route->__get('validate');
            $packet_data = $route->__get('id');
            $packet_data = "{\"id\":\"" . $packet_data . "\"}";
        } else {
            $token = $request->header('token');
            $style = $request->header('style');
            $md5_client = $request->header('validate');
            $packet_data = $this->request_data($request);
        };
        if (empty($packet_data)) $packet_data = "{}";
        $md5_server = md5($style . $token . $packet_data . APP_REQUEST_CHECK_CODE);

       dd($md5_server);
//        dd($packet_data);
//        print_r($style . $token . $packet_data . APP_REQUEST_CHECK_CODE);

        $result = ($md5_client === $md5_server);
//        dd($result);
        return $result;
    }

    protected function request_data($request) {
        $result = file_get_contents('php://input');
        if (empty($result) && ($request->method() == 'GET') && ($request->header('weixin') == '10')) {
            // 小程序 客户端
            $data = [];
//            $_SERVER['QUERY_STRING'] = 'code=0114g4Tm053Adr1uyTUm06xoTm04g4Tw&a=1&b=1';
//            $_SERVER['QUERY_STRING'] = '';
            parse_str($_SERVER['QUERY_STRING'], $data);
            $result = json_encode($data);
            if (empty($data)) $result = '';
//            dd($_SERVER['QUERY_STRING']);
//            dd($result);
        }
        return $result;
    }
}
