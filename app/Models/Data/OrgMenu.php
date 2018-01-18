<?php

namespace App\Models\Data;

class OrgMenu extends Menu {
    protected $table = DB_PREFIX . 'org_menu';
    protected $role = DB_PREFIX . 'org_role';

}
