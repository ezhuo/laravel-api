<?php

namespace App\Http\Controllers\Data;

use App\Models\Data\Canton;
use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\Frame\AppDataController;

class CantonController extends AppDataController {
    public function __construct(Request $request, Canton $model) {
        parent::__construct($request, $model);
        $this->middleware('auth', ['except' => ['get_selectselectselect']]);
    }

    public function index(Request $request) {
        $data['list'] = $this->model->canton_data($request);
        $data['count'] = count($data['list']);
//        dd( $data['list'] );
        return return_json($data);
    }

    public function get_selectselectselect(Request $request, $id) {
        $data = $this->model->canton_selectselectselect($request, $id);
        return return_json($data);
    }


    public function destroy(Request $request, $id) {
        $http_code = HTTP_WRONG;
        $num = 0;
        if (empty($id)) {
            return return_json([], '数据请求为空，操作失败', $http_code);
        }
        $dataset = $this->model->find($id);
        if (!empty($dataset)) {
            $num = DB::delete("delete from " . $this->model_table . " where fdn like ? ", [$dataset->fdn . "%"]);
            $http_code = ($num ? HTTP_OK : HTTP_WRONG);
            if ($num) canton_cache_clear();
            return return_json(['result' => $num], ($http_code == HTTP_OK ? "删除成功" : "删除失败"), $http_code);
        }
        return return_json(null, "未找到要删除的数据！", HTTP_WRONG);
    }

    protected function after_update($request, $dataset, $id) {
        $pk = $this->model_key;
        $canton_id = $id;
        if ((!empty($dataset->parent_id)) && (!empty($canton_id))) {
            //更新当前节点
            $obj = Canton::select(['text_name'])->find($dataset->parent_id);
            $text_name = sprintf('%s|%s', $obj->text_name, $dataset->name);
            Canton::where(array($pk => $canton_id))->update(['text_name' => $text_name]);

            canton_cache_clear();
            //更新级联节点
            //$where[$pk] = $data['canton_id'];
            //$fdn = $this->where($where)->getField('fdn');
            //$sql = sprintf("update __TABLE__ set text_name = convert(func_canton_textName(fdn) using utf8) where %s != %s and fdn like '%s'" , $pk , $data['canton_id'] , $fdn . '%' );
            //$this->execute($sql);
        }
        return true;
    }

    protected function after_store($request, $dataset, $id) {
        $pk = $this->model_key;
        $canton_id = $id;
        if (empty($dataset->parent_id)) {
            $p_fdn = "";
            $canton_uniqueno = "";
            $text_name = $dataset->name;
        } else {
            $p_info = Canton::select(['fdn', 'text_name', 'canton_uniqueno'])->find($dataset->parent_id)->toArray();//父fdn
            $p_fdn = $p_info['fdn'];
            $text_name = sprintf('%s|%s', $p_info['text_name'], $dataset->name);
            $p_canton_uniqueno = $p_info['canton_uniqueno'];
            if (empty($p_canton_uniqueno)) {
                $sql = "select max(canton_uniqueno) as canton_uniqueno from " . $this->model_table . " where LENGTH(canton_uniqueno) > 2 ";
                $p_info = DB::select($sql);
                if (!empty($p_info)) {
                    $p_canton_uniqueno = $p_info[0]->canton_uniqueno;
                }
            }
            $canton_uniqueno = intval($p_canton_uniqueno) + 1;
        }

        $canton_id = str_pad(intval($id), APP_CANTON_LENGTH, 0, STR_PAD_LEFT);
        $fdn = sprintf('%s%s.', $p_fdn, $canton_id);
        $new_data = array('fdn' => $fdn, 'text_name' => $text_name, 'canton_uniqueno' => $canton_uniqueno);
        Canton::where(array($pk => $id))->update($new_data);
        canton_cache_clear();
        return true;
    }

}
