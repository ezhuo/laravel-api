<?php
namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;

class Tokens extends Model {
    public $timestamps = false;
    protected $table = DB_PREFIX . 'sys_token';
    protected $primaryKey = 'token_id';
    protected $keyType = 'string';

    protected $hidden = ['created_at'];
    protected $fillable = [
        "token_id",
        "token_info",
        "auth_id",
        "token_source",
        "token_monitor",
        "token_expire",
        "token_data",
        "created_at"
    ];

    public static function read($token) {
        $id = md5($token);
        $result = Tokens::where(['token_id' => $id])->where('TOKEN_EXPIRE', '>', time())->select(['token_info'])->first();
        if ($result) Tokens::where(['token_id' => $id])->update(['TOKEN_EXPIRE' => time() + TOKEN_EXPIRE]);
        return $result;
    }

    public static function read_auth_id($request, $auth_id) {
        return Tokens::where([
            'auth_id' => $auth_id,
            'token_source' => $request->__source,
            'token_monitor' => $request->__monitor
        ])->where('TOKEN_EXPIRE', '>', time())->first();
    }

    /**
     * 返回
     * @param $request
     * @param $token
     * @param $auth_id
     * @return mixed
     */
    public static function write($request, $token, $auth_id) {
        return Tokens::insert([
            'token_id' => md5($token),
            'token_info' => $token,
            'auth_id' => $auth_id,
            'token_source' => $request->__source,
            'token_monitor' => $request->__monitor,
            'token_expire' => time() + TOKEN_EXPIRE,
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
        return Tokens::where(['token_id' => md5($token)])->delete();
    }

    /**
     * 返回影响行数
     * @param $request
     * @param $auth_id
     * @return mixed
     */
    public static function destroy_auth_id($request, $auth_id) {
        return Tokens::where([
            'auth_id' => $auth_id,
            'token_source' => $request->__source,
            'token_monitor' => $request->__monitor
        ])->delete();
    }


}
