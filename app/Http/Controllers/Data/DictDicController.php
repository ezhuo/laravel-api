<?php

namespace App\Http\Controllers\Data;

use Illuminate\Http\Request;
use App\Models\Data\DictDic;
use App\Http\Controllers\Frame\AppDataController;

class DictDicController extends AppDataController {

    public function __construct(Request $request, DictDic $model) {
        parent::__construct($request, $model);
        $this->middleware('auth');
    }


}
