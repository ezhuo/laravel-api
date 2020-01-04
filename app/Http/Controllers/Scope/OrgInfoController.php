<?php

namespace App\Http\Controllers\Scope;

use App\Http\Controllers\Frame\AppDataController;
use App\Models\Data\Canton;
use App\Models\Scope\OrgInfo;
use DB;
use Illuminate\Http\Request;

class OrgInfoController extends AppDataController
{
    public function __construct(Request $request, OrgInfo $model)
    {
        parent::__construct($request, $model);
        $this->middleware('auth');
    }

    protected function get_fields($request, $dataset)
    {
        return [
            DB::raw($this->model_table . '.*'),
            DB::raw('b.text_name as canton_text_name'),
            DB::raw('c.parent_org_name'),
        ];
    }

    protected function get_export_fields($request, $dataset)
    {
        return $this->get_fields($request, $dataset);
    }

    protected function get_where($request, $dataset)
    {
        $where = parent::get_where($request, $dataset);
        if ($request['org_fdn%']) {
            $where['dataset'] = $where['dataset']->whereRaw($this->model_table . ".org_fdn like ?", [$request['org_fdn%'] . '%']);
        }
        $where['join'] = [
            [
                'join' => 'leftJoin',
                'table' => DB::raw('(select canton_id,fdn,text_name from sys_canton) as b'),
                'left' => DB::RAW($this->model_table . '.canton_fdn'),
                'ex' => '=',
                'right' => DB::RAW('b.fdn'),
            ],
            [
                'join' => 'leftJoin',
                'table' => DB::raw('(select org_id as parent_org_id,org_name as parent_org_name from org_info) as c'),
                'left' => DB::RAW($this->model_table . '.parent_id'),
                'ex' => '=',
                'right' => DB::RAW('c.parent_org_id'),
            ],
        ];
        return $where;
    }

    protected function parse_create($request, $dataset, $id)
    {
        if (empty($request['canton_fdn'])) {
            $request['canton_fdn'] = APP_CANTON_FDN;
            $request['canton_id'] = APP_CANTON_ID;
        }
        if (!isset($request['canton_id']) && !empty($request['canton_fdn'])) {
            $request['canton_id'] = Canton::get_id_byfdn($request['canton_fdn']);
        }
        return parent::parse_create($request, $dataset, $id); // TODO: Change the autogenerated stub
    }

    protected function after_store($request, $dataset, $id)
    {
        if ($id) {
            $dataset = $dataset->find($id);
            if (empty($request['parent_id'])) {
                $dataset->org_fdn = $dataset->org_id . '.';
            } else {
                $dataset->org_fdn = $request['parent_id'] . '.' . $dataset->org_id . '.';
            }
            $dataset->save();
            $result = $dataset->org_init($request, $id);
        }
        return parent::after_store($request, $dataset, $id); // TODO: Change the autogenerated stub
    }

    public function index_tree(Request $request)
    {
        $request['tree'] = 1;
        return parent::__index_dict(
            $request,
            [DB::Raw('org_id,' . $this->model_table . '.parent_id,' . $this->model_table . '.org_fdn as `key` , org_name as title , org_name as text')],
            ['order' => ['org_id' => 'asc', $this->model_table . '.org_fdn' => 'asc']]
        );
    }

    protected function __index_dict(Request $request, $p_field = [], $p_where = [], $p_dict_display = [])
    {
        return parent::__index_dict($request, ['org_fdn'], ['order' => ['orderby' => 'asc', 'org_fdn' => 'asc', 'org_id' => 'asc']], $p_dict_display);
        // return parent::__index_dict($request, ['org_fdn'], $p_where, $p_dict_display);
    }
}
