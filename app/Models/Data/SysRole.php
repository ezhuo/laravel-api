<?php

namespace App\Models\Data;

use App\Models\Frame\Data;

class SysRole extends Data {
    protected $table = DB_PREFIX . 'sys_role';
    protected $primaryKey = 'role_id';
    protected $dict_value = 'name';

    protected $rules_setting = [
        'rules' => [
            'name' => 'bail|required|min:2|unique',
            'menu_ids' => 'bail|required|min:1',
            'level' => 'bail|required|min:1',
        ],
        'field' => [
            'name' => '角色名称',
            'menu_ids' => '选择功能菜单',
            'level' => '选择菜单分类',

        ]
    ];

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        return;
    }

    public function get_order_field() {
        return ['role_id' => 'asc'];
    }


}
