<?php

namespace App\Models\Auth;

use App\Models\Data\SysRole;
use App\Models\Frame\Data;
use App\Models\Scope\OrgInfo;
use Illuminate\Support\Facades\DB;

class Auth extends Data
{
    protected $table = DB_PREFIX . '';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        return;
    }

    public static function loginPc($request)
    {
        $user_name = $request['name'];
        $pwd = $request['password'];
        $account = null;
        if (empty($user_name) || empty($pwd)) {
            return ['account' => $account, 'code' => 201];
        }

        $where['login_username'] = $user_name;
        $where['login_pwd'] = password_encode($pwd);
        $where['status'] = 10;

        $account = DB::table(DB_PREFIX . 'sys_account')->where($where)
            ->first([
                'account_id as id', 'login_username', 'true_name', 'phone', 'email',
                'role_id', 'org_id', 'group_ids', 'group_names', 'images',
            ]);
        if (empty($account)) {
            return ['account' => $account, 'code' => 202];
        }

        $account->role_id = $account->role_id . '';
        if (!empty($account->role_id)) {
            $roleObj = SysRole::find($account->role_id);
            if ($roleObj) {
                $account->role_name = $roleObj->name;
            }
        };
        if (!empty($account->org_id)) {
            $orgObj = OrgInfo::find($account->org_id);
            if ($orgObj) {
                $account->org_name = $orgObj->org_corpname;
                $account->org_fdn = $orgObj->org_fdn;
                $account->is_group = $orgObj->is_group;
            }
        };
        $account->admin = ($account->role_id == '1');
        $account->style = $request['login_type'];
        $account->sessionid = uniqid();
        return ['account' => $account, 'code' => HTTP_OK];
    }

    public static function loginApp($request)
    {
        return self::loginPc($request);
    }
    
}
