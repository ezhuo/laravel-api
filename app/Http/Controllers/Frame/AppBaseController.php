<?php

namespace App\Http\Controllers\Frame;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Frame\Base;
use Illuminate\Validation\Rule;
use Log;
use Illuminate\Support\Facades\Route;
use App\Models\Data\SysSetting;
use App\Models\Data\DictSetting;


class AppBaseController extends BaseController {
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * 当前时间
     * @var null|string
     */
    protected $dt = null;

    /**
     * 当前数据模型
     * @var null
     */
    protected $model = null;

    protected $model_table = null;

    protected $model_key = 'id';

    protected $model_key_is_uuid = false;

    protected $export_field = [];

    protected $export_public_list = 'public.export_list';

    protected $export_public_item = 'public.export_item';

    protected $success_update = null;

    protected $success_store = null;

    protected $success_destroy = null;

    protected $error_update = null;

    protected $error_store = null;

    protected $error_destroy = null;

//    protected $sys_dic = null;

//    protected $dict_dic = null;

    public function __construct(Request $request, Base $model) {
        $this->dt = get_dt();

        $this->model = $model;

        if ($this->model) {
            $this->model_table = $model->getTable();
            $this->model_key = $this->get_model_key();
            $this->model_key_is_uuid = $this->model->get_primary_is_uuid();
            $this->export_field = $this->model->get_export_fields();
        }

        $this->success_update = function ($request, $dataset, $id) {
            $this->after_update($request, $dataset, $id);//更新
        };

        $this->success_store = function ($request, $dataset, $id) {
            $this->after_store($request, $dataset, $id);//更新
        };

        $this->success_destroy = function ($request, $dataset, $id) {
            $this->after_destroy($request, $dataset, $id);//删除
        };

        return null;
    }

    /**
     * {@inheritdoc}
     */
    protected function formatValidationErrors(Validator $validator) {
        $message = $validator->errors()->all();
//        dd($message2);

//        $idx = -1;
//        $failed = $validator->failed();
//        foreach ($failed as $k => $v) {
//            $msgkey = [];
//            $msgkey[] = $k;
//            $idx++;
//            foreach ($v as $kk => $vv) {
//                $msgkey [] = $kk;
//            }
//            $kk = strtolower(implode('.', $msgkey));
//            if (array_key_exists($kk, $this->rules_setting['message']) && false) {
//                $message[] = $this->rules_setting['message'][$kk];
//            } else {
//                $message[] = $message2[$idx];
//            }
//        };

        return [
            'data' => [],
            'message' => $message,
        ];
    }

    /**
     * 获取当前数据集的字段
     * @param $request
     * @param $dataset
     * @return array
     */
    protected function get_fields($request, $dataset) {
        return ['*'];
    }

    /**
     * 获取当前查询条件
     * egt_ , elt_ , gt_ , lt_ , in_ , %%
     * @param $request
     * @param $dataset
     * @return array
     */
    protected function get_where($request, $dataset) {
        $where = [];
        $db_fields = $this->get_table_fields($dataset);
//        dd($db_fields);
        $request_data = $request->all();
        foreach ($request_data as $key => $val) {
//            if (strlen($val) < 1 && empty($val)) continue;
            $fieldAdd = "eq";
            if (substr($key, 0, 4) == "egt_") {
                $key = substr($key, 4);
                $fieldAdd = "egt";
            } else if (substr($key, 0, 4) == "elt_") {
                $key = substr($key, 4);
                $fieldAdd = "elt";
            } else if (substr($key, 0, 3) == "gt_") {
                $key = substr($key, 3);
                $fieldAdd = "gt";
            } else if (substr($key, 0, 3) == "lt_") {
                $key = substr($key, 3);
                $fieldAdd = "lt";
            } else if (substr($key, 0, 3) == "in_") {
                $key = substr($key, 3);
                $val = explode(',', $val);
                $fieldAdd = "in";
            } else if (strpos($key, "%") !== false) {
                if ($key[0] == '%') {
                    $val = '%' . $val;
                    $key = substr($key, 1);
                }
                if ($key[strlen($key) - 1] == '%') {
                    $val = $val . '%';
                    $key = substr($key, 0, -1);
                }
                $fieldAdd = "like";
            }
            if (!empty($fieldAdd) && !empty($key) && in_array($key, $db_fields))
                $where[$fieldAdd][$key] = $val;
        };

//        /**
//         * 获取分页
//         */
        $where['limit'] = request_page($request);

        if (check_not_empty($request, '_order')) {
            $tmpOrder = explode(':', $request['_order']);
            $where['order'][$tmpOrder[0]] = $tmpOrder[1];
        } else if (check_empty($request, 'order')) {
            $where['order'] = $dataset->get_order_field();
        }

        $where['dataset'] = $dataset;
        return $where;
    }

    /**
     * 获取当前dataset的表字段
     * @param $dataset
     * @return mixed
     */
    protected function get_table_fields($dataset) {
        if (is_string($dataset))
            return Base::get_table_fields($dataset);
        return Base::get_table_fields($dataset->getTable());
    }

    /**
     * 获取key
     * @param $dataset
     * @return mixed
     */
    protected function get_table_key($dataset) {
        if (is_orm($dataset))
            return $dataset->getKeyName();
        else if (is_builder($dataset))
            return $dataset->getModel()->getKeyName();
        return false;
    }

    protected function get_model_key($dataset = null) {
        if (empty($dataset)) {
            $dataset = $this->model;
        }
        if (is_orm($dataset))
            return $dataset->getKeyName();
        else if (is_builder($dataset))
            return $dataset->getModel()->getKeyName();
        return false;
    }

    /**
     * 不管系统级或用户级的数据字典，都获取到
     * @param $request
     */
    protected function get_dic($request, $is_arrayToArray = false) {
        $cache_key = __FUNCTION__;
        $result = S($cache_key);
        if (empty($result)) {
            $sys_dic = $this->get_sys_dic($request, $is_arrayToArray);
            $dict_dic = $this->get_dict_dic($request, $is_arrayToArray);
            if (empty($sys_dic)) $sys_dic = [];
            if (empty($dict_dic)) $dict_dic = [];
            $result = array_merge($sys_dic, $dict_dic);
            S($cache_key, $result);
        }
        return $result;
    }

    /**
     * 获取系统级设置表
     */
    protected function get_sys_dic($request, $is_arrayToArray = false) {
        return $this->get_dict($request, 'sys_dic', $is_arrayToArray);
    }

    /**
     * 获取用户级设置表 ， 如果使用 arrayToArray ，将会出现一个较为严重的问题，就是不能排序
     * @param $request
     */
    protected function get_dict_dic($request, $is_arrayToArray = false) {
        return $this->get_dict($request, 'dict_dic', $is_arrayToArray);
    }

    protected function get_dict($request, $table_name, $is_arrayToArray = false) {
        $org_id = 0;
        if ($request && $table_name != 'sys_dic' && isset($request->__user)) {
            if (check_not_empty($request->__user, DB_ORG_ID)) {
                $org_id = $request->__user->{DB_ORG_ID};
            }
        }
        //如果机构ID为空，则没有必要返回
        if (($table_name == 'dict_dic') && empty($org_id)) return [];
        $cache = $table_name . "_" . $org_id . '_' . ($is_arrayToArray ? '1' : '0');
//        dd($cache);
        $result = S($cache);
        if (empty($result)) {
            $where = [];
            if ($org_id) $where[DB_ORG_ID] = $org_id;
            $where['is_del'] = 0;
            $where['show_type'] = 1;
            $result = DB::table($table_name)
                ->where($where)
                ->orderBy('type')
                ->orderBy('order')
                ->orderBy('code')
                ->orderBy('name')
                ->get(['type', 'code', 'name']);
//            dd($result);
            if ($is_arrayToArray) {
//                dd($is_arrayToArray);
                $result = arrayToArray($result);
            } else
                $result = arrayToArrayByType($result);

            S($cache, $result);
        }
        return $result;
    }

    protected function get_setting($request, $table = 'sys') {
        $data = [];

        if ($table == 'sys')
            $ds = SysSetting::where(['org_id' => 0]);
        else
            $ds = DictSetting::where(['org_id' => 0]);

        if (property_exists($request->__user, 'org_id')) {
            $ds = $ds->orWhere(['org_id' => $request->__user->org_id]);
        }

        $ds = $ds->where(['is_del' => 0])->get(['type', 'name', 'code']);
        if ($ds) {
            $data = arrayToArray($ds);
        }
        return $data;
    }

    // ----------------------------------------------------------

    protected function search($request, $dataset, $where = [], $fields = [], $is_array = false, $id = null) {
        //解析查询条件

        $dataset = $this->parse_where($request, $dataset, $where);
        if (empty($fields)) $fields = ['*'];

        if (empty($id)) {
            //数据列表=---------------
            $data['list'] = null;
            //获取数据个数
            $data['total'] = $this->get_data_count($request, $dataset);

            //数据导出判断
            $is_export = $this->check_export_data($request, $dataset, $data['total']);
            if ($is_export == -1) {
                return return_json($data, '当前数据共有' . $data['total'] . '条记录，超出了最大为' . EXPORT_MAX_COUNT . '条限制，导出失败，操作将终止！', HTTP_VALIDATE);
            } else if ($is_export == 1) {
                //获取数据导出
                return $this->get_export_data($request, $dataset, $where);
            } else if (isset($request[TREE_HTTP_CODE]) && !empty($request[TREE_HTTP_CODE])) {
                //tree数据
                $data['list'] = $this->get_tree_data($request, $dataset, $where);
            }

            //获取数据列表
            if (empty($data['list']))
                $data['list'] = $this->get_data_load($request, $dataset, $where, $fields, $is_array);
            return return_json($data, "success");
        } else {
            //查询单个对象---------------
            return $this->__show($request, $dataset, $fields, $id);
        }
    }

    /**
     * 解析当前查询条件
     * @param $request
     * @param $dataset
     * @param array $where
     * @return mixed
     */
    protected function parse_where($request, $dataset, $where) {
        $table_alias = '';
        if (is_builder($dataset)) {
            $table_alias = $dataset->getModel()->get_table_alias();
        } else if (is_orm($dataset)) {
            $table_alias = $dataset->get_table_alias();
        }

        $_ff = function ($__kkk) use ($table_alias) {
            if (!empty($table_alias)) {
                return DB::raw($table_alias . '.' . $__kkk);
            }
            return $__kkk;
        };

        if (isset($where['dataset'])) {
            unset($where['dataset']);
        }
        //----------------------------

        if (isset($where['eq']) && $where['eq']) {
            foreach ($where['eq'] as $k => $v) {
                $dataset = $dataset->where($_ff($k), '=', $v);
            }
        }
        if (isset($where['neq']) && $where['neq']) {
            foreach ($where['neq'] as $k => $v)
                $dataset = $dataset->where($_ff($k), '!=', $v);
        }
        if (!empty($where['eqor'])) {
            $dataset = $dataset->where(function ($query) use ($where, $_ff) {
                foreach ($where['eqor'] as $lk => $lv)
                    $query->orWhere($_ff($lk), '=', $lv);
            });
        }
        // ----------------------------
        //大于
        if (isset($where['gt']) && $where['gt']) {
            foreach ($where['gt'] as $k => $v)
                $dataset = $dataset->where($_ff($k), '>', $v);
        }
        //大于等于
        if (isset($where['egt']) && $where['egt']) {
            foreach ($where['egt'] as $k => $v)
                $dataset = $dataset->where($_ff($k), '>=', $v);
        }
        //小于
        if (isset($where['lt']) && $where['lt']) {
            foreach ($where['lt'] as $k => $v)
                $dataset = $dataset->where($_ff($k), '<', $v);
        }
        //小于等于
        if (isset($where['elt']) && $where['elt']) {
            foreach ($where['elt'] as $k => $v)
                $dataset = $dataset->where($_ff($k), '<=', $v);
        }

        // ----------------------------

        if (isset($where['in']) && $where['in']) {
            foreach ($where['in'] as $k => $v)
                $dataset = $dataset->whereIn($_ff($k), $v);
        }
        if (isset($where['nin']) && $where['nin']) {
            foreach ($where['nin'] as $k => $v)
                $dataset = $dataset->whereNotIn($_ff($k), $v);//
        }

        // ----------------------------
        if (isset($where['between']) && $where['between']) {
            foreach ($where['between'] as $k => $v)
                $dataset = $dataset->whereBetween($_ff($k), $v);
        }

        // ----------------------------
        if (isset($where['like']) && $where['like']) {
            $dataset = $dataset->where(function ($query) use ($where, $_ff) {
                foreach ($where['like'] as $lk => $lv)
                    $query->where($_ff($lk), 'like', $lv);
            });
        }
        if (isset($where['nlike']) && $where['nlike']) {
            $dataset = $dataset->where(function ($query) use ($where, $_ff) {
                foreach ($where['nlike'] as $lk => $lv)
                    $query->where($_ff($lk), 'not like', $lv);
            });
        }
        if (isset($where['likeor']) && $where['likeor']) {
            $dataset = $dataset->where(function ($query) use ($where, $_ff) {
                foreach ($where['likeor'] as $lk => $lv)
                    $query->orWhere($_ff($lk), 'like', $lv);
            });
        }

        if (isset($where['null']) && $where['null']) {
            foreach ($where['null'] as $k => $v)
                $dataset = $dataset->whereNull($_ff($k))->orwhere($_ff($k), $v);
        }
        // ----------------------------
        if (check_not_empty($where, 'join')) {
            if (check_not_empty($where['join'], 'join')) {
                $join = $where['join'];
                $dataset = $dataset->{$join['join']}($join['table'], $join['left'], $join['ex'], $join['right']);
            } else {
                foreach ($where['join'] as $join) {
                    $dataset = $dataset->{$join['join']}($join['table'], $join['left'], $join['ex'], $join['right']);
                }
            }
        }
//        dd($dataset);
        return $dataset;
    }

    /**
     * 解析request到数据模型
     * @param $request
     * @param $dataset
     * @param $id
     * @return array
     */
    protected function parse_create($request, $dataset, $id) {
        //检查字段映射
        $code = 200;
        $message = "";

        //检测提交字段的合法性
        $is_orm = is_orm($dataset);

        $rules_setting = $this->parse_rules($request, $dataset, $id);
        if (!empty($rules_setting['rules'])) {
            $this->validate($request,
                $rules_setting['rules'],
                $rules_setting['message'],
                $rules_setting['field']
            );
        }

        //获取字段
        if ($is_orm) {
            $Fillfields = $dataset->getFillable();
            if (empty($Fillfields)) {
                $Fillfields = $this->get_table_fields($dataset->getTable());
            }
        } else if (is_string($dataset)) {
            $Fillfields = $this->get_table_fields($dataset);
        }

        $request_data = [];
        if ($id == 0 && !check_not_empty($request, DB_CREATED_AT)) {
            $request[DB_CREATED_AT] = $this->dt;
        }
        if ($id > 0 && !check_not_empty($request, DB_UPDATED_AT)) {
            $request[DB_UPDATED_AT] = $this->dt;
        }
        if ($request[DB_CREATED_AT] < '2000-1-1') {
            $request[DB_CREATED_AT] = $this->dt;
        }
        if ($request[DB_UPDATED_AT] < '2000-1-1') {
            $request[DB_UPDATED_AT] = $this->dt;
        }

        foreach ($Fillfields as $col) {
            if (isset($request[$col])) {
                $request_val = $request[$col];
                $request_data[$col] = $request_val;
                if ($is_orm) {
                    $dataset->{$col} = $request_val;
                }
            }
        }
        return ['data' => $request_data, 'code' => $code, 'message' => $message];
    }

    protected function parse_rules($request, $dataset, $id = 0) {
        $rules_setting = $dataset->get_rules_setting();

        foreach ($rules_setting['rules'] as $key => $val) {
            $arr = [];
            if (is_string($val)) {
                $arr = explode('|', $val);
            } else {
                $arr = $val;
            }
            $field = $val;

            //array 的value 为unique
            if (in_array('unique', $arr)) {
                $field = [];
                foreach ($arr as $kkk => $vvv) {
                    $rule = $vvv;
                    if ($vvv == 'unique') {
                        $rule = Rule::unique($dataset->getTable())->ignore($id, $dataset->getKeyName());
                    };
                    $field[] = $rule;
                }
            } else if (array_key_exists('unique', $arr)) {
                $field = [];
                foreach ($arr as $kkk => $vvv) {
                    $rule = $vvv;
                    if ($kkk == 'unique' || $vvv == 'unique') {
                        $rule = Rule::unique($dataset->getTable())->ignore($id, $dataset->getKeyName());
                        if (is_array($vvv)) {
                            $is_true = false;
                            if (array_key_exists('eq', $vvv)) {
                                $is_true = true;
                                foreach ($vvv['eq'] as $wc => $wv)
                                    $rule = $rule->where($wc, $wv);
                            }
                            if (array_key_exists('neq', $vvv)) {
                                $is_true = true;
                                foreach ($vvv['neq'] as $wc => $wv)
                                    $rule = $rule->whereNot($wc, $wv);
                            }
                            if (!$is_true) {
                                foreach ($vvv as $wc => $wv)
                                    $rule = $rule->whereNot($wc, $wv);
                            }
                        }
                    };
                    $field[] = $rule;
                }
            }
            $rules_setting['rules'][$key] = $field;
        };

        Log::info(($dataset->getTable() ? $dataset->getTable() : "") . " >> ");
//        Log::info($rules_setting);

        return $rules_setting;
    }

    protected function get_excel($dataset, $path, $title, $data, $dict, $filename) {
        $field = [];
        if ($dataset) {
            if (is_builder($dataset)) {
                $field = $dataset->getModel()->get_export_fields();
            } else if (is_orm($dataset)) {
                $field = $dataset->get_export_fields();
            }
        }
        return return_excel($path, $title, $field, $data, $dict, $filename);
    }

    // ----------------------------------------------------------


    /**
     * 从数据库，加载当前列表中的数据
     * @param $request
     * @param $dataset
     * @param array $where
     * @param array $fields
     * @param bool $is_array
     * @return array
     */
    protected function get_data_load($request, $dataset, $where = [], $fields = [], $is_array = false) {
        $list = [];
        if (check_not_empty($where, 'limit')) {
            $dataset = $dataset->skip($where['limit']['page'])->take($where['limit']['size']);
        }

        if (check_not_empty($where, 'order')) {
            foreach ($where['order'] as $lk => $lv) {
                $dataset = $dataset->orderBy($lk, $lv);
            }
        }

        if ($is_array) {
            $list = $dataset->get($fields)->toArray();
        } else {
            $list = $dataset->get($fields);
        }

        return $list;
    }

    /**
     * 从数据库，加载当前列表中的个数
     * @param $request
     * @param $dataset
     * @return mixed
     */
    protected function get_data_count($request, $dataset) {
        return $dataset->take(1)->count();
    }

    // ----------------------------------------------------------

    /**
     * 检查当前是否需要导出 0 不用导出 1:导出 -1：导出失败
     * @param $request
     * @param $dataset
     * @param $count
     * @return bool
     */
    protected function check_export_data($request, $dataset, $count) {
        $result = 0;

        $is_true = false;
        foreach (EXPORT_HTTP_CODE as $code) {
            $is_true = (isset($request[$code]) && $request[$code] == '1');
            if ($is_true) break;
        }
        if ($is_true) {
            $result = 1;
            if ($count > EXPORT_MAX_COUNT) {
                return -1;
            }
        }
        return $result;
    }

    /**
     * 获取当前导出EXCEL数据集的字段
     * @param $request
     * @param $dataset
     * @return array
     */
    protected function get_export_fields($request, $dataset) {
        return ['*'];
    }

    /**
     * 获取当前导出时，需要辅助的数据字典
     * @param $request
     * @param $dataset
     * @return array
     */
    protected function get_export_dict($request, $dataset, $is_arrayToArray = true) {
        return $this->get_dic($request, $is_arrayToArray);
    }

    /**
     * 数据导出
     * @param $request
     * @param $dataset
     * @param $list
     */
    protected function get_export_data($request, $dataset, $where = []) {
        $title = $request['export_title'];
        $filename = urldecode($request['export_file']);

        $dataset = $dataset->skip(0)->take(EXPORT_MAX_COUNT);

        if (check_not_empty($where, 'order')) {
            foreach ($where['order'] as $lk => $lv) {
                $dataset = $dataset->orderBy($lk, $lv);
            }
        }

        $fields = $this->get_export_fields($request, $dataset);
        $dict = $this->get_export_dict($request, $dataset);

        $list = $dataset->get($fields)->toArray();
        return $this->get_excel($dataset, $this->export_public_list, $title, $list, $dict, $filename);
    }

    /**
     * 返回树数据
     * @param $request
     * @param $dataset
     * @param array $where
     * @param array $fields
     * @param bool $is_array
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    protected function get_tree_data($request, $dataset, $where = []) {
        //获取数据列表
        return [];
    }

    // ----------------------------------------------------------
    //列表
    protected function __index($request, $dataset, $fields = [], $where = [], $id = null) {
        //获取查询条件
        $get_where = $this->get_where($request, $dataset);
        if (isset($get_where['dataset'])) {
            $dataset = $get_where['dataset'];
            unset($get_where['dataset']);
        }
        $where = array_merge($get_where, $where);
        //获取字段
        if (empty($fields))
            $fields = $this->get_fields($request, $dataset);

        return $this->search($request, $dataset, $where, $fields, false, $id);
    }

    //获取数据字典
    protected function __index_dict(Request $request, $p_field = [], $p_where = []) {
        $vv = $this->model->get_dict_value();
        if (is_array($vv)) {
            $field = array_merge([$this->model->get_dict_key()], $vv);
        } else {
            $field = [$this->model->get_dict_key(), $vv];
        }
        $field = array_merge($field, $p_field);
        $where = ['limit' => ['page' => 0, 'size' => 10000]];
        $where = array_merge($where, $p_where);

        $dataset = $this->model;
        if (empty($vv)) {
            return $this->__index($request, $dataset);
        } else {
            return $this->__index(
                $request,
                $dataset,
                $field,
                $where
            );
        }
    }

    //数据更新
    protected function __update($request, $dataset, $id = 0, $success = null, $error = null) {
        $is_orm = is_orm($dataset);
        $last_id = false;

        if ($is_orm) {
            $last_id = $this->__update_orm($request, $dataset, $id);
        } else {
            $last_id = $this->__update_db($request, $dataset, $id);
        }

        if (!$last_id) {
            if (isset($error) && !empty($error)) {
                $fn_res = call_user_func($error, $request, $dataset, $last_id);
            }
            return return_json([], '数据操作失败', HTTP_WRONG);
        } else {
//            if (!isset($fn)) {
//                $fn = function ($request, $dataset, $last_id) use ($id) {
//                    if ($id) {
//                        $this->after_update($request, $dataset, $last_id);//更新
//                    } else {
//                        $this->after_store($request, $dataset, $last_id);//新增
//                    }
//                };
//            }

            //回调处理函数
            if (isset($success) && !empty($success)) {
                $fn_res = call_user_func($success, $request, $dataset, $last_id);
            }

            if ($is_orm) {
                $last_id = $dataset->find($last_id);
            }
            return return_json(['result' => $last_id], '数据操作成功');
        }
    }

    /**
     * 更新ORM数据元
     * @param $request
     * @param $dataset
     * @param $id
     * @return bool|\Illuminate\Contracts\Routing\ResponseFactory|null|string|\Symfony\Component\HttpFoundation\Response
     */
    protected function __update_orm($request, $dataset, $id) {
        $last_id = null;

        //更新前，先找到原数据
        if (!empty($id)) {
            $dataset = $dataset->find($id);
        }

        $request_data = $this->parse_create($request, $dataset, $id);
        if ($request_data['code'] != 200) {
            return return_json([], $request_data['message'], HTTP_WRONG);
        };

        if (!empty($id))
            $this->before_update($request, $dataset, $id);
        else {
            //主键是UUID
            if ($dataset->get_primary_is_uuid()) {
                $uuid = get_uuid();
                $dataset->{$this->get_model_key($dataset)} = $uuid;
                //取消自增
                $dataset->setIncrementing(false);
            }
            $this->before_store($request, $dataset);
        }

        $res = $dataset->save();
        Log::info('result-res:' . $dataset->getTable() . '>>' . $res);
        Log::info('result-dataset:' . $dataset->getTable() . '>>' . $dataset);

        if ($res) {
            $last_id = $dataset->{$dataset->getKeyName()};
//            if (empty($id) && $dataset->get_primary_is_uuid()) {
//                $last_id = $uuid;
//            } else {
//                $last_id = $dataset->{$dataset->getKeyName()};
//            }
        }
        return $last_id;
    }

    /**
     * 更新DB数据元
     * @param $request
     * @param $dataset
     * @param $id
     * @return bool|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    protected function __update_db($request, $dataset, $id) {
        $last_id = false;
        $uuid = "";

        $request_data = $this->parse_create($request, $dataset, $id);
        if ($request_data['code'] != 200) {
            return return_json([], $request_data['message'], HTTP_WRONG);
        };

        if (!$id) {
            $last_id = $dataset->insert($request_data['data'])->lastInsertId();
        } else {
            $res = $dataset->where(['id' => $id])->update($request_data['data']);
            if ($res) {
                $last_id = $id;
            }
        }
        return $last_id;
    }

    //数据删除
    protected function __destroy($request, $dataset, $id, $success = null, $error = null) {
        $http_code = HTTP_WRONG;
        $num = 0;
        if (empty($id)) {
            return return_json([], '数据请求为空，操作失败', $http_code);
        }
        $dataset = $dataset->find($id);
        if (!empty($dataset)) {
            $this->before_destroy($request, $dataset, $id);
            $num = $dataset->delete(); //返回bool值

            //删除回调
            if ($num && isset($success) && !empty($success)) {
                call_user_func($success, $request, $dataset, $id);
            }

            if (!$num && isset($error) && !empty($error)) {
                call_user_func($error, $request, $dataset, $id);
            }

            $http_code = ($num ? HTTP_OK : HTTP_WRONG);
        }
        return return_json(['result' => $num], ($http_code == HTTP_OK ? "删除成功" : "删除失败"), $http_code);
    }

    //数据显示
    protected function __show($request, $dataset, $fields = [], $id = null) {
        if (empty($id)) {
            return return_json([], '数据请求为空，操作失败', HTTP_WRONG);
        }
        $result = $dataset->select($fields)->find($id);
        return return_json(($result ? $result : []), ($result ? "success" : "数据没找到！"), ($result ? HTTP_OK : HTTP_WRONG));
    }

    /**
     * http : /get/{id}  , 用途：导出单个数据元
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    protected function export_item($request, $dataset, $id, $title, $filename) {
        $list = $dataset->find($id);
        $dict = $this->get_export_dict($request, $dataset);
        return $this->get_excel($dataset, $this->export_public_item, $title, $list, $dict, $filename);
    }

    /**
     * http : /get/{id}  , 用途：导出单个数据元
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    protected function export_list($request, $dataset, $fields = [], $where = []) {
        $request[EXPORT_HTTP_CODE[0]] = '1';
        return $this->__index($request, $dataset, $fields, $where);
    }

    // ----------------------------------------------------------

    // 新增之前
    protected function before_store($request, $dataset) {
        return $this->before_update($request, $dataset, 0);
    }

    // 新增之后
    protected function after_store($request, $dataset, $id) {
        return $this->after_update($request, $dataset, $id);
    }

    //更新之前
    protected function before_update($request, $dataset, $id) {
        return true;
    }

    //更新之后
    protected function after_update($request, $dataset, $id) {
        return true;
    }

    //删除之前
    protected function before_destroy($request, $dataset, $id) {
        return true;
    }

    //删除之后
    protected function after_destroy($request, $dataset, $id) {
        return true;
    }

    /**
     * 获取工单编号
     */
    protected function get_order_no() {
        $res = DB::select('select func_order_no() as no;');
        if ($res && count($res) > 0) {
            return $res[0]->no;
        }
        return '';
    }

    /**
     * 获取当前控制器与方法
     *
     * @return array
     */
    protected function getCurrentAction() {
        $action = Route::current()->getActionName();
        list($class, $method) = explode('@', $action);

        $con = explode('\\', $class);
        $class = array_pop($con);
        return ['controller' => $class, 'method' => $method];
    }

}