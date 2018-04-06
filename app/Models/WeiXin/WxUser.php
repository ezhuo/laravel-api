<?php

namespace App\Models\WeiXin;

use Illuminate\Support\Facades\DB;
use App\Models\Frame\Data;

class WxUser extends Data {
    protected $table = DB_PREFIX . 'wx_user';
    protected $tableAlias = DB_PREFIX . 'wx_user';
    protected $primaryKey = 'openid';
    protected $keyType = 'string';

    protected $fillable = [
        'openid',
        'nickName',
        'avatarUrl',
        'gender',
        'city',
        'province',
        'country',
        'userInfo',
        'created_at'
    ];

    protected $rules_setting = [
        'rules' => [
            'openid' => 'bail|min:1|unique',
            'avatarUrl' => 'bail|required|min:1',
            'nickName' => 'bail|min:1',
        ],
        'field' => [
            'openid' => '微信登录OPENID',
            'avatarUrl' => '微信图片',
            'nickName' => '微信昵称',
        ]
    ];

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        return;
    }

    public static function write($request) {
        $userInfo = array_merge($request->input('userInfo'), $request->input('loginData'));
        $wxUser = self::find($userInfo['openid']);
        if ($wxUser) {
            return $wxUser;
        }
        $model = new WxUser();
        $fileds = self::get_table_fields($model->table);
        foreach ($userInfo as $f => $v) {
            if (in_array($f, $fileds)) {
                $model->{$f} = $userInfo[$f];
            }
        }
        return $model->save();
    }

}
