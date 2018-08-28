<?php

namespace App\Models\Data;

use Illuminate\Support\Facades\DB;
use App\Models\Frame\Data;

class Menu extends Data
{

    protected $role = null;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        return;
    }

    public function get_menu($account)
    {
        return $this->format_Menu($account);
    }

    public function format_Menu($usr)
    {
        $result = array();
        $menu_all = [];
        $result['case'] = $this->format_menu_list('case', $menu_all, $usr);
        $result['sys'] = $this->format_menu_list('sys', $menu_all, $usr);
        return $result;
    }

    protected function format_menu_list($kind, $menu_all, $usr)
    {
        $menu = $this->get_menu_list($kind, $usr);
        $tree = [];
        if (sizeof($menu) > 0) {
            $tree = getTree($menu, 0, 'menu_id', 'parent_id', 'children');
        }
        return $tree;

        // print_r($menu_all);die();
        // foreach ($menu as $val) {
        //     $tree[$val['fdn']] = $val;
        //     $tree[$val['fdn']]['children'] = array();
        // }

        // foreach ($tree as $k => $item) {
        //     $parent_fdn = $this->get_parent_fdn($item['fdn']);
        //     if ($parent_fdn) {
        //         if (array_key_exists($parent_fdn, $tree)) {
        //             $tree[$parent_fdn]['children'][] = $tree[$k];
        //             unset($tree[$k]);
        //         } else {
        //             if (array_key_exists($parent_fdn, $menu_all)) {
        //                 $tree[$parent_fdn] = $menu_all[$parent_fdn];
        //                 $tree[$parent_fdn]['children'][] = $tree[$k];
        //                 unset($tree[$k]);
        //             }
        //         }
        //     }
        // }

        // return $tree;
    }

    private function get_parent_fdn($current_fdn)
    {
        $arr = explode('.', $current_fdn);
        if (sizeof($arr) < 3) {
            return null;
        } else {
            array_pop($arr);
            array_pop($arr);
            $arr[] = '';
            return implode('.', $arr);
        }
    }

    public function get_menu_list($kind, $usr)
    {
        $menu = [];
        $where['status'] = 1;
        $where['type'] = $kind;

        if (!empty($usr)) {
            $ids = DB::table($this->role)->where(['role_id' => $usr->role_id])->first(['menu_all_ids']);
            if (empty($ids)) {
                return $menu;
            }

            $ids = $ids->menu_all_ids;
            $ids = empty($ids) ? "0" : $ids;
            $ids = explode(',', $ids);
        }

        $menuCacheName = 'menu_' . $this->table . $kind . $usr->role_id;
        $menu = S($menuCacheName);
        if (empty($menu)) {
            $menu = $this->where($where);
            if (!empty($ids)) $menu = $menu->whereIn('menu_id', $ids);
            $menu = $menu->orderBy('idx', 'asc')
                ->orderBy('fdn', 'asc')
//                    ->distinct()
                ->get(['menu_id', 'fdn', DB::raw('title as text'), 'api', 'link', 'icon', 'top', 'parent_id'])->toArray();
            S($menuCacheName, $menu);
        }
        return $menu;
    }

    public function get_menu_all()
    {
        $menu = S($this->table . '_all');
        if (empty($menu)) {
            $menu = $this->where(array('status' => 1))
                ->select(['menu_id', 'fdn', DB::raw('title as text'), 'api', 'link', 'icon', 'top', 'parent_id'])
                ->orderBy('idx', 'asc')
                ->orderBy('fdn', 'asc')
                ->get()->toArray();
            $menu_all = [];
            foreach ($menu as $val) {
                $menu_all[$val['fdn']] = $val;
            }
            $menu = $menu_all;
            S($this->table . '_all', $menu);
        }
        return $menu;
    }

    public function get_menu_log()
    {
        $menu = S($this->table . '_log');
        if (empty($menu)) {
            $menu = $this->where(array('status' => 1))
                ->select([DB::raw('LOWER(api) as api'), 'title'])
                ->orderBy('idx', 'asc')
                ->orderBy('fdn', 'asc')
                ->get();
            $menu = arrayToArray($menu);
            S($this->table . '_log', $menu);
        }
        return $menu;
    }

}
