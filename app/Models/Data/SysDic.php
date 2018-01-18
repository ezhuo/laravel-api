<?php

namespace App\Models\Data;

use App\Models\Frame\Data;

class SysDic extends Data {
    protected $table = DB_PREFIX . 'sys_dic';
    protected $primaryKey = 'dic_id';

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        return;
    }


}
