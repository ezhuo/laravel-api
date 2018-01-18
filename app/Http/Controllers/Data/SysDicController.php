<?php

namespace App\Http\Controllers\Data;

use App\Models\Data\SysDic;
use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\Frame\AppDataController;

class SysDicController extends AppDataController {

    public function __construct(Request $request, SysDic $model) {
        parent::__construct($request, $model);
        $this->middleware('auth');
    }

    /**
     * http: /get , 用途：数据
     * @param Request $request
     */
    public function tree(Request $request, $id) {
        $ds = $this->model;
        $map = [];
        $result = $ds->where($map)->select([
            DB::raw("'#' as parent"),
            "type", "order",
            DB::raw("type_name as text")
        ])->whereRaw("type_name != ''")->distinct()->orderBy('order', 'asc')->orderBy('type_name', 'desc')->get();

        $data['list'] = $result;
        $data['total'] = sizeof($data['list']);
        return return_json($data);
    }

    protected function get_where($request, $dataset) {
        $where = parent::get_where($request, $dataset);
        $where['order'] = ['order' => 'asc', 'code' => 'asc'];
        return $where;
    }


}
