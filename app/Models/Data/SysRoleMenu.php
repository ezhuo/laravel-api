<?php

namespace App\Models\Data;

use App\Models\Frame\Data;
class SysRoleMenu extends Data {
    protected $table = DB_PREFIX . 'sys_role';
    protected $primaryKey = 'role_id';



    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        return;
    }

    public function get_order_field() {
        return ['role_id' => 'asc'];
    }


}
