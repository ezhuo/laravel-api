<?php

namespace App\Http\Controllers\Data;

use Illuminate\Http\Request;
use App\Models\Data\DictDic;
use App\Http\Controllers\Frame\AppDataController;
use DB;

class DictDicController extends AppDataController
{

    public function __construct(Request $request, DictDic $model)
    {
        parent::__construct($request, $model);
        $this->middleware('auth');
    }

    public function tree(Request $request, $id)
    {
        $ds = $this->model;
        $map = ['org_id' => $request->__user->org_id];
        $result = $ds->where($map)->select([
            "type", "type_name"
        ])->whereRaw("type_name != ''")->distinct()->orderBy('order', 'asc')->orderBy('type_name', 'desc')->get();

        $data['list'] = $result;
        $data['total'] = sizeof($data['list']);
        return return_json($data);
    }

    protected function get_where($request, $dataset)
    {
        $where = parent::get_where($request, $dataset);
        $where['order'] = ['order' => 'asc', 'code' => 'asc'];
        return $where;
    }


}
