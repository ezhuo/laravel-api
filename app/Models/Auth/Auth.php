<?php

namespace App\Models\Auth;

use Illuminate\Support\Facades\DB;
use App\Models\Frame\Data;
use App\Models\Data\Canton;
use App\Models\Scope\OrgInfo;
use App\Models\Data\SysRole;

class Auth extends Data
{
    protected $table = DB_PREFIX . '';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        return;
    }

    public static function login_pc_sys($request)
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
                "account_id as id", "login_username", "true_name", "phone", "email",
                "role_id", "org_id", "group_ids", "group_names", "images"
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
            }
        };
        $account->admin = ($account->role_id == '1');
        $account->style = $request['login_type'];
        $account->sessionid = uniqid();
        return ['account' => $account, 'code' => HTTP_OK];
    }

    public static function login_pc_org($request)
    {
        $user_name = $request['name'];
        $pwd = $request['password'];
        $account = null;
        if (empty($user_name) || empty($pwd)) {
            return ['account' => $account, 'code' => 201];
        }

        $where['mobi'] = $user_name;
        $where['login_pwd'] = password_encode($pwd);

        $account = DB::table(DB_PREFIX . 'org_account')->where($where)->whereIn('status', ['10', '20', '30'])
            ->first([
                "org_account_id as id", "org_id", "org_fdn",
                "dept_id", "dept_fdn", "group_id", "group_fdn",
                "canton_id", "canton_fdn",
                "mobi", "true_name", "email",
                "role_id", "images",
            ]);
        if (empty($account)) {
            return ['account' => $account, 'code' => 202];
        }

        if (!empty($account->canton_fdn)) {
            $account->canton_name = Canton::get_name_byfdn($account->canton_fdn);
        }


        if (!empty($account->org_id)) {
            $account->org_name = OrgInfo::get_name_byid($account->org_id);
        }
        $account->role_id = $account->role_id . '';
        $account->admin = ($account->role_id == '1');
        $account->style = $request['login_type'];
        $account->sessionid = uniqid();
        return ['account' => $account, 'code' => 200];
    }
}
