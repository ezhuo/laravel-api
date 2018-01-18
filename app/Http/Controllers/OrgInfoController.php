<?php

namespace App\Http\Controllers;

use App\Models\Data\Canton;
use Illuminate\Http\Request;
use App\Models\OrgInfo;
use DB;
use App\Http\Controllers\Frame\AppDataController;

class OrgInfoController extends AppDataController {

    public function __construct(Request $request, OrgInfo $model) {
        parent::__construct($request, $model);
        $this->middleware('auth');
    }

    protected function get_fields($request, $dataset) {
        return [
            DB::raw($this->model_table . '.*'),
            DB::raw('b.text_name as canton_text_name')
        ];
    }

    protected function get_export_fields($request, $dataset) {
        return $this->get_fields($request, $dataset);
    }

    protected function get_where($request, $dataset) {
        $where = parent::get_where($request, $dataset);
        $where = $this->get_default_where($request, $where);

        if ($request['org_state'] == 'true') {
            $map['org_state'] = 1;
        }

        $where['join'] = [
            'join' => 'leftJoin',
            'table' => DB::raw('sys_canton as b'),
            'left' => DB::RAW($this->model_table . '.canton_fdn'),
            'ex' => '=',
            'right' => DB::RAW('b.fdn')
        ];
        return $where;
    }

    protected function parse_create($request, $dataset, $id) {
        if (empty($request['canton_fdn'])) {
            $request['canton_fdn'] = APP_CANTON_FDN;
            $request['canton_id'] = APP_CANTON_ID;
        }
        if (!isset($request['canton_id']) && !empty($request['canton_fdn'])) {
            $request['canton_id'] = Canton::get_id_byfdn($request['canton_fdn']);
        }
        return parent::parse_create($request, $dataset, $id); // TODO: Change the autogenerated stub
    }

    protected function after_store($request, $dataset, $id) {
        if ($id) {
            $dataset = $dataset->find($id);
            if (empty($dataset->fdn)) {
                $dataset->fdn = $dataset->org_id . '.';
                $dataset->save();
            }
            $result = $dataset->org_init($request, $id);
        }
        return parent::after_store($request, $dataset, $id); // TODO: Change the autogenerated stub
    }

}
