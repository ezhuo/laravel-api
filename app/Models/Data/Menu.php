<?php

namespace App\Models\Data;

use Illuminate\Support\Facades\DB;
use App\Models\Frame\Data;

class Menu extends Data {

    protected $role = null;

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);

        return;
    }

    public function get_menu($account) {
        return $this->format_Menu($account);
    }

    public function format_Menu($usr) {
        $result = array();
        $menu_all = $this->get_menu_all();
        $result['case'] = $this->format_menu_list('case', $menu_all, $usr);
        $result['sys'] = $this->format_menu_list('sys', $menu_all, $usr);
        return $result;
    }

    protected function format_menu_list($kind, $menu_all, $usr) {
        $menu = $this->get_menu_list($kind, $usr);
        $tree = array();
        foreach ($menu as $val) {
            $tree[$val['fdn']] = $val;
            $tree[$val['fdn']]['children'] = array();
        }

        foreach ($tree as $k => $item) {
            $parent_fdn = $this->get_parent_fdn($item['fdn']);
            if ($parent_fdn) {
                if (array_key_exists($parent_fdn, $tree)) {
                    $tree[$parent_fdn]['children'][] = $tree[$k];
                    unset($tree[$k]);
                } else {
                    if (array_key_exists($parent_fdn, $menu_all)) {
                        $tree[$parent_fdn] = $menu_all[$parent_fdn];
                        $tree[$parent_fdn]['children'][] = $tree[$k];
                        unset($tree[$k]);
                    }
                }
            }
        }

        return $tree;
    }

    private function get_parent_fdn($current_fdn) {
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

    public function get_menu_list($kind, $usr) {
        $menu = [];
        $where['status'] = 1;
        $where['type'] = $kind;
        if (empty($usr)) {
            $menu = S($this->table . $kind);
            if (empty($menu)) {
                $menu = $this->where($where)
                    ->orderBy('idx', 'asc')
                    ->orderBy('fdn', 'asc')
//                    ->distinct()
                    ->get(['menu_id', 'fdn', 'title', 'api', 'sref', 'icon', 'top'])->toArray();
                S('sys_menu_' . $kind, $menu);
            }
        } else {
            $ids = DB::table($this->role)->where(['role_id' => $usr->role_id])->first(['menu_ids']);
            if (empty($ids)) {
                return $menu;
            }
            $ids = $ids->menu_ids;
            $ids = empty($ids) ? "0" : $ids;
            $ids = explode(',', $ids);
            $where = [];
            $where['type'] = $kind;
            $where['status'] = 1;
            $menu = $this->where($where);
//            if ($usr->role_id != 1)
            $menu = $menu->whereIn('menu_id', $ids);
            $menu = $menu->orderBy('idx', 'asc')
                ->orderBy('fdn', 'asc')
                ->get(['menu_id', 'fdn', 'title', 'api', 'sref', 'icon', 'top'])->toArray();
        }
        return $menu;
    }

    public function get_menu_all() {
        $menu = S($this->table . '_all');
        if (empty($menu)) {
            $menu = $this->where(array('status' => 1))
                ->select(['menu_id', 'fdn', 'title', 'api', 'sref', 'icon', 'top'])
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

    public function get_menu_log() {
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
