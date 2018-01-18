<?php

namespace App\Http\Controllers\Test;

use Illuminate\Http\Request;
use App\Models\OrgInfo;
use App\Http\Controllers\Frame\AppDataController;

class TestController extends AppDataController {

    public function __construct(Request $request, OrgInfo $model) {
        parent::__construct($request, $model);
        // $this->middleware('auth');
    }

    public function index(Request $request){
        return return_json($request->header(),'error',200);
    }

    protected function get_fields($request, $dataset) {
        $fields = parent::get_fields($request, $dataset);
        return $fields;
    }


    protected function get_where($request, $dataset) {
        $where = parent::get_where($request, $dataset);
        return $where;
    }

    protected function get_data_load($request, $dataset, $where = [], $fields = [], $is_array = false) {
        return parent::get_data_load($request, $dataset, $where, $fields, $is_array);
    }

    protected function get_data_count($request, $dataset) {
        return parent::get_data_count($request, $dataset);
    }

    protected function get_export_fields($request, $dataset) {
        return parent::get_export_fields($request, $dataset);
    }

    protected function get_export_data($request, $dataset, $where = []) {
        return parent::get_export_data($request, $dataset, $where);
    }

    // 新增之后
    protected function after_store($request, $dataset, $id) {
        return true;
    }

    //更新之后
    protected function after_update($request, $dataset, $id) {
        return true;
    }

    //删除之后
    protected function after_destroy($request, $dataset, $id) {
        return true;
    }

    public function post(Request $request){
        return return_json($request->all(),'succss');
    }

}
