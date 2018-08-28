<?php

namespace App\Http\Controllers\Data;

use Illuminate\Http\Request;
use App\Models\Data\SysMenu;
use App\Models\Data\SysRole;
use DB;
use App\Http\Controllers\Frame\AppDataController;

class SysMenuController extends AppDataController
{

    public function __construct(Request $request, SysMenu $model)
    {
        parent::__construct($request, $model);
        $this->middleware('auth');
    }

    protected function get_where($request, $dataset)
    {
        $map = parent::get_where($request, $dataset);
        $map['eq']['status'] = 1;
        return $map;
    }

    public function get_menu_list(Request $request)
    {
        $role_id = $request['role_id'];

        $ids = null;
        if ($role_id) {
            $ids = SysRole::find($role_id);
            if ($ids) {
                $ids = $ids->menu_ids;
            }
        }

        if ($ids)
            $ids = explode(',', $ids);

        $sql = "select menu_id as `key` , fdn , parent_id , title  from sys_menu where `status` = 1 order by idx,fdn";

        $arr = DB::select($sql);
        $arr = object2array($arr);

        $treeData = getTree($arr, 0, 'key', 'parent_id', 'children');
        $selData = [];
        if ($ids) {
            foreach ($ids as $val) {
                $selData[] = intval($val);
            }
        }
        return return_json(['list' => $treeData, 'sel' => $selData], '');
    }

}
