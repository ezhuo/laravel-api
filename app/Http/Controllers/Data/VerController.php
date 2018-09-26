<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Frame\AppDataController;
use App\Models\Frame\Base;
use Illuminate\Http\Request;

class VerController extends AppDataController
{
    public function __construct(Request $request, Base $model)
    {
        parent::__construct($request, $model);
        $this->middleware('auth', ['except' => ['CheckVersion']]);
    }

    public function CheckVersion(Request $request)
    {
        return return_json([
            'ver' => '1.0.1',
            'message' => '数据更新！',
            'url' => 'http://down.ylyapp.cn:89/apk/resthome-qqb-v1.x.apk',
            'total' => '',
            'errcode' => 0,
        ], '');
    }

}
