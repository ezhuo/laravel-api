<?php

namespace App\Http\Controllers;

use App\Models\ViewOrderProcessInfo;
use App\Models\OrgInfo;
use App\Models\ResthomeInfo;
use Illuminate\Http\Request;
use DB;
use App\Models\OlderInfo;
use App\Http\Controllers\Frame\AppDataController;
use App\Models\Frame\Data;

class SysDashController extends AppDataController {

    public function __construct(Request $request, Data $model) {
        parent::__construct($request, $model);
        $this->middleware('auth');
    }

}
