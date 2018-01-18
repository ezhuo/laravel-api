<?php

namespace App\Http\Controllers\Data;

use Illuminate\Http\Request;
use App\Models\Data\SysSetting;
use App\Http\Controllers\Frame\AppDataController;

class SysSettingController extends AppDataController {

    public function __construct(Request $request, SysSetting $model) {
        parent::__construct($request, $model);
        $this->middleware('auth');
    }

}
