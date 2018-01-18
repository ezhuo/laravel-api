<?php

namespace App\Models\Data;

use App\Models\Frame\Data;

class SysMessageInfo extends Data {
    protected $table = DB_PREFIX . 'sys_message_info';
    protected $primaryKey = 'id';

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        return;
    }


}
