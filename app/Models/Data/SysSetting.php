<?php

namespace App\Models\Data;

use App\Models\Frame\Data;
class SysSetting extends Data {
    protected $table = DB_PREFIX . 'sys_setting';
    protected $primaryKey = 'set_id';

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        return;
    }


}
