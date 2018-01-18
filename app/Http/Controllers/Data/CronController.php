<?php

namespace App\Http\Controllers\Data;

use App\Models\Frame\Base;
use Illuminate\Http\Request;
use App\Http\Controllers\Frame\AppDataController;

class CronController extends AppDataController {

    public function __construct(Request $request, Base $model) {
        return parent::__construct($request, $model);
    }

}
