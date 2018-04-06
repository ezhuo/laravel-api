<?php
namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;

class WxTokens extends Model {
    public $timestamps = false;
    protected $table = DB_PREFIX . 'wx_token';
    protected $primaryKey = 'token_id';
    protected $keyType = 'string';

    protected $hidden = ['created_at'];
    protected $fillable = [
        "token_id",
        "token_info",
        "openid",
        "session_key",
        "token_source",
        "token_monitor",
        "token_expire",
        "token_data",
        "created_at"
    ];

    public static function read($token) {
        $id = md5($token);
        $result = self::where(['token_id' => $id])->where('TOKEN_EXPIRE', '>', time())->select(['token_info', 'openid'])->first();
        if ($result) self::where(['token_id' => $id])->update(['TOKEN_EXPIRE' => time() + TOKEN_WEIXIN_EXPIRE]);
        return $result;
    }

    public static function read_auth_id($request, $open_id) {
        return self::where([
            'openid' => $open_id,
            'token_source' => $request->__source,
            'token_monitor' => $request->__monitor
        ])->where('TOKEN_EXPIRE', '>', time())->first();
    }

    /**
     * 返回
     * @param $request
     * @param $token
     * @param $open_id
     * @return mixed
     */
    public static function write($request, $session_key, $open_id, $code = null, $userInfo = '') {
        if (!is_string($userInfo)) {
            $userInfo = json_encode($userInfo);
        }
        self::destroy($session_key);
        return self::insert([
            'token_id' => md5($session_key),
            'token_info' => $userInfo,
            'openid' => $open_id,
            'code' => $code,
            'session_key' => $session_key,
            'token_source' => $request->__source,
            'token_monitor' => $request->__monitor,
            'token_expire' => time() + TOKEN_WEIXIN_EXPIRE,
            'created_at' => get_dt()
        ]);
    }

    /**
     * 返回影响行数
     * @param array|int $token
     * @return mixed
     */
    public static function destroy($token) {
        if (empty($token)) return false;
        return self::where(['token_id' => md5($token)])->delete();
    }

    /**
     * 返回影响行数
     * @param $request
     * @param $open_id
     * @return mixed
     */
    public static function destroy_auth_id($request, $open_id) {
        return self::where([
            'openid' => $open_id,
            'token_source' => $request->__source,
            'token_monitor' => $request->__monitor
        ])->delete();
    }


}