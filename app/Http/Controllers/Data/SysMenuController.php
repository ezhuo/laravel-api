<?php

namespace App\Http\Controllers\Data;

use Illuminate\Http\Request;
use App\Models\Data\SysMenu;
use App\Models\Data\SysRole;
use DB;
use App\Http\Controllers\Frame\AppDataController;

class SysMenuController extends AppDataController {

    public function __construct(Request $request, SysMenu $model) {
        parent::__construct($request, $model);
        $this->middleware('auth');
    }

    protected function get_where($request, $dataset) {
        $map = parent::get_where($request, $dataset);
        $map['eq']['status'] = 1;
        return $map;
    }

    public function get_menu_list(Request $request) {
        $role_id = $request['role_id'];
        $ids = null;
        $where_ids = '0';

        if ($role_id) {
            $ids = SysRole::find($role_id);
            if ($ids) {
                $ids = $ids->menu_ids;
            }
        }
        if ($ids) {
            $where_ids = " menu_id in ($ids) ";
        }

        $sql = "
            select a.menu_id as id,fdn,title as text , b.selected  from
	          ( select * from sys_menu where `STATUS` = 1 ) a
	              LEFT join ( select menu_id , 'selected' as selected from sys_menu where $where_ids and  `STATUS` = 1 ) b
		            ON  a.menu_id = b.menu_id  order by idx,fdn
        ";

//        die($sql);
        $arr = DB::select($sql);
        $arr = object2array($arr);

        $result = array();
        foreach ($arr as $val) {
            $tmp = explode('.', $val['fdn']);
            if (sizeof($tmp) == 2) {
                $val['type'] = 'root';
                $result[$val['fdn']] = $val;
            } else if (sizeof($tmp) == 3) {

                if (isset($result[$tmp[0] . "."])) {
                    $result[$tmp[0] . "."]['children'][$val['fdn']] = $val;
                }
            } else if (sizeof($tmp) == 4) {
                if (isset($result[$tmp[0] . "."]['children'][$tmp[0] . "." . $tmp[1] . "."])) {
                    $result[$tmp[0] . "."]['children'][$tmp[0] . "." . $tmp[1] . "."]['children'][$val['fdn']] = $val;
                }
            }
        }

        return return_json($this->tree_data($result), '');
    }

    private function tree_data($arr) {
        $result = array();
        foreach ($arr as $val) {
            if (!empty($val['children'])) {
                $val['children'] = array_values($this->tree_data($val['children']));
            }
            $result[] = $val;
        }
        return $result;
    }

}
