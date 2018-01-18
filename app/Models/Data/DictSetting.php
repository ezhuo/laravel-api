<?php

namespace App\Models\Data;

use App\Models\Frame\Data;

class DictSetting extends Data {
    protected $table = DB_PREFIX . 'dict_setting';
    protected $primaryKey = 'set_id';

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        return;
    }

}
