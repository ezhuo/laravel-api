<?php

namespace App\Models\Data;

use Illuminate\Support\Facades\DB;
use App\Models\Frame\Data;

class SysAccount extends Data {
    protected $table = DB_PREFIX . 'sys_account';
    protected $tableAlias = DB_PREFIX . 'sys_account';
    protected $primaryKey = 'account_id';

    protected $fillable = [
        "canton_id",
        "canton_fdn",
        "resthome_id",
        "login_username",
        "login_pwd",
        "true_name",
        "tel",
        "email",
        "address",
        "role_id",
        "images",
        "shorcut_ids",
        "menu_ids",
        "desk_ids",
        "main_url",
        "status",
        "user_type",
        "start_time",
        "end_time",
        "delete_status",
        "creater_user_id",
        "created_at"
    ];

    protected $rules_setting = [
        'rules' => [
            'login_username' => 'bail|min:1|unique',
            'role_id' => 'bail|required|min:1',
            'true_name' => 'bail|min:1',
            'canton_fdn' => 'bail|min:1',
        ],
        'field' => [
            'login_username' => '登录帐号',
            'role_id' => '角色',
            'true_name' => '真实姓名',
            'canton_fdn' => '所属区域',
        ]
    ];

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        return;
    }

    public function check_pwd($id, $key, $new, $action) {
        $where = [];
        //1:修改密码
        if ($action == 1) {
            if (empty($id) || empty($key)) return false;
            $where['login_pwd'] = password_encode($key);
        } else {
            //2:重置密码
            $new = '123456';
        }

        $obj = $this->where($where)->find($id);
        if (empty($obj)) {
            return false;
        } else if (!empty($new)) {
            $obj->login_pwd = password_encode($new);
//            dd($new);
            $res = $obj->save();
//            dd($obj->login_pwd);
            if ($res) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    public static function addUser($request, $config) {
        $result = null;
        if (!empty($config['resthome_id'])) {
            $data['resthome_id'] = $config['resthome_id'];
            $data['login_username'] = $config['rh_simple'];
            $data['true_name'] = $config['rh_name'];
            $data['canton_fdn'] = $config['canton_fdn'];
            $data['role_id'] = 6;
            $data['user_type'] = 2;
            $data['status'] = 1;
            $data['login_pwd'] = password_encode('123456');
            $result = SysAccount::firstOrCreate($data);
        }
        return $result;
    }

}
