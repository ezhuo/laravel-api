<?php

namespace App\Http\Controllers\WeiXin;

use App\Http\Controllers\Frame\AppDataController;
use Illuminate\Http\Request;
use App\Models\Frame\Base;
use Log;
use DB;

class AppWxController extends AppDataController {

    /**
     * @var string
     */
    private $appId;
    private $secret;
    private $code2session_url;
    private $sessionKey;

    public function __construct(Request $request, Base $model) {
        parent::__construct($request, $model);
        $this->appId = weixin_appid;
        $this->secret = weixin_secret;
        $this->code2session_url = weixin_code2session_url;
    }

    public function decode(Request $request) {
        //encryptedData 和 iv 在小程序端使用 wx.getUserInfo 获取
        $encryptedData = $request['encryptedData'];
        $iv = $request['iv'];
        $code = $request['code'];
        return $this->decoding($code, $encryptedData, $iv);
    }

    /**
     * Created by vicleos
     * @return mixed
     */
    protected function getLoginInfo($code) {
        return $this->authCodeAndCode2session($code);
    }

    /**
     * Created by vicleos
     * @param $encryptedData
     * @param $iv
     * @return string
     * @throws \Exception
     */
    protected function decoding($code, $encryptedData, $iv) {
        if (!empty($code)) {
            $this->sessionKey = S('code_' . $code);
        }
        include_once "./WxBizData/wxBizDataCrypt.php";
        $pc = new \WXBizDataCrypt($this->appId, $this->sessionKey);
        $decodeData = "";
        $errCode = $pc->decryptData($encryptedData, $iv, $decodeData);
        if ($errCode != 0) {
            return [
                'code' => 10001,
                'message' => 'encryptedData 解密失败'
            ];
        }
        return $decodeData;
    }

    /**
     * Created by vicleos
     * 根据 code 获取 session_key 等相关信息
     * @throws \Exception
     */
    private function authCodeAndCode2session($code) {
        $code2session_url = sprintf($this->code2session_url, $this->appId, $this->secret, $code);

        $userInfo = $this->httpRequest($code2session_url);
        if (!isset($userInfo['session_key'])) {
            return false;
        }
        S('code_' . $code, $userInfo['session_key']);
        $this->sessionKey = $userInfo['session_key'];
        return $userInfo;
    }

    /**
     * 请求小程序api
     * @author 晚黎
     * @date   2017-05-27T11:51:10+0800
     * @param  [type]                   $url  [description]
     * @param  [type]                   $data [description]
     * @return [type]                         [description]
     */
    private function httpRequest($url, $data = []) {
//        $curl = curl_init();
//        curl_setopt($curl, CURLOPT_URL, $url);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
//        if (!empty($data)) {
//            curl_setopt($curl, CURLOPT_POST, 1);
//            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
//        }
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//        Log::info("URL:" . $url);
//        $output = curl_exec($curl);
        $output = get_url_content($url, $data, 'GET');
        if ($output === FALSE) {
            return false;
        }
//        curl_close($curl);
        return json_decode($output, JSON_UNESCAPED_UNICODE);
    }

}