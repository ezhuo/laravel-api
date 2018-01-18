<?php

namespace App\Models\Data;

class SysMenu extends Menu {
    protected $table = DB_PREFIX . 'sys_menu';
    protected $role = DB_PREFIX . 'sys_role';

}
