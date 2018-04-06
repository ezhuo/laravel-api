<?php

namespace App\Http\Controllers\WeiXin;

use Illuminate\Http\Request;
use App\Models\Frame\Base;

class WxPublicController extends AppWxController {

    public function __construct(Request $request, Base $model) {
        parent::__construct($request, $model);
        $this->middleware('auth');
    }

}
