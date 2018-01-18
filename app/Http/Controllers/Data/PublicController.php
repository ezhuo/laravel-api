<?php

namespace App\Http\Controllers\Data;

use App\Models\Frame\Base;
use Illuminate\Http\Request;
use DB;
use Cache;
use App\Http\Controllers\Frame\AppDataController;

class PublicController extends AppDataController {

    public function __construct(Request $request, Base $model) {
        parent::__construct($request, $model);
        $this->middleware('auth');
    }

    /**
     * 清除缓存
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function cache_clear(Request $request) {
        Cache::flush();
        return return_json([], '缓存清理完毕！', HTTP_DONE);
    }

    public function get_sys_dict(Request $request) {
        return return_json(
            [
                'sys_dic' => $this->get_sys_dic($request),
                'sys_setting' => $this->get_setting($request),
                'app_canton_fdn' => APP_CANTON_FDN,
                'app_canton_name' => APP_CANTON_NAME,
                'app_canton_id' => APP_CANTON_ID
            ]
        );
    }

    public function get_dict_dict(Request $request) {
        return return_json([
            'dict_dic' => $this->get_dict_dic($request),
            'dict_setting' => $this->get_setting($request, 'dic'),
        ]);
    }


}
