<?php

namespace App\Http\Controllers\Frame;

use App\Models\Data\SysMessageInfo;
use App\Models\Frame\Base;
use Illuminate\Http\Request;

class AppDataController extends AppBaseController
{

    public function __construct(Request $request, Base $model)
    {
        parent::__construct($request, $model);
        return;
    }

    /**
     * http : /get , 用途：列表
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $dataset = $this->model;
        return $this->__index($request, $dataset);
    }

    /**
     * http : /get , 用途：dict列表
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function index_dict(Request $request)
    {
        return $this->__index_dict($request);
    }

    /**
     * http : /get/create ,
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request)
    {
        return return_json();
    }

    /**
     * http : /post , 用途：创建数据元
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request)
    {
        $dataset = $this->model;
        return $this->__update($request, $dataset, 0, $this->success_store);
    }

    /**
     *  http : /put/{id} , 用途：更新单个数据元
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function update(Request $request, $id)
    {
        $dataset = $this->model;
        return $this->__update($request, $dataset, $id, $this->success_update);
    }

    /**
     *  http : get/{id} , 用途：显示单个数据元
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function show(Request $request, $id)
    {
        $dataset = $this->model;
//        return $this->__show($request, $dataset, $id);
        return $this->__index($request, $dataset, [], [], $id);
    }

    /**
     *  http : get/{id}/edit , 用途：显示要编译的数据元
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(Request $request, $id)
    {
        $dataset = $this->model;
        return $this->__index($request, $dataset, [], [], $id);
    }

    /**
     *  http : /DELETE/{id} , 用途：删除单个数据元
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function destroy(Request $request, $id)
    {
        $dataset = $this->model;
        return $this->__destroy($request, $dataset, $id, $this->success_destroy);
    }

    /**
     * http : /get , 用途：导出数据列表
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function exports(Request $request, $id, $style, $token, $validate)
    {
        $title = $request['export_title'];
        $filename = $request['export_file'];
        $dataset = $this->model;
        if (!empty($id)) {
            //导出单个元素
            return $this->export_item($request, $dataset, $id, $title, $filename);
        } else {
            //导出列表
            return $this->export_list($request, $dataset);
        }
    }

    /**
     * http: /get , 用途：树
     * @param Request $request
     */
    public function tree(Request $request, $id)
    {
        $dataset = $this->model;
        $request[TREE_HTTP_CODE] = 1;
        return $this->__index($request, $dataset);
    }

    protected function get_where($request, $dataset)
    {
        $where = parent::get_where($request, $dataset);
        return $this->get_default_where($request, $dataset, $where);
    }

    /**
     * 获取默认的查询
     * @param $request
     */
    protected function get_default_where($request, $dataset, $where)
    {
        if (empty($where)) {
            $where = [];
        }

        $current_controller = $this->getCurrentAction()['controller'];
        if (!isset($where['like'])) {
            $where['like'] = [];
        }

        if (!isset($where['eq'])) {
            $where['eq'] = [];
        }

        switch ($request->__user->style) {

            case 'sys':
                //后台-----------------------
                if (!$request->__user->admin) {
                    $fileds = $this->get_table_fields($dataset);
                    if (!empty($request->__user->org_fdn) && in_array('org_fdn', $fileds)) {
                        $where['like']['org_fdn'] = $request->__user->org_fdn . '%';
                    }
                }
                break;

            case 'app':
                //---------------------------
                if (check_empty($request, 'org_id')) {
                    $where['eq']['org_id'] = $request->__user->org_id;
                }

                break;
        }

        return $where;
    }

    /**
     * 消息写入
     * @param $request
     * @param $dataset
     * @param $url
     * @param string $content
     */
    protected function sys_message_add(
        $request,
        $order_id,
        $account_id,
        $role_id,
        $url,
        $content,
        $canton_fdn,
        $resthome_id = 0,
        $org_id = 0,
        $dept_fdn = '',
        $group_fdn = ''
    ) {

        //写入前，先把以前该订单所有消息取消
        SysMessageInfo::where(['order_id' => $order_id, 'status' => '0'])->where('order_status', '<', $request['order_status_new'])->update(['status' => 1]);
        $message = new SysMessageInfo();
        $message->order_status = $request['order_status_new'];
        $message->order_id = $order_id;
        $message->account_id = $account_id;
        $message->role_id = $role_id;
        $message->content = $content;
        $message->url = $url;
        $message->canton_fdn = $canton_fdn;
        $message->resthome_id = $resthome_id;
        $message->org_id = $org_id;
        $message->dept_fdn = $dept_fdn;
        $message->group_fdn = $group_fdn;
        $message->status = 0;
        $message->creater_user_id = $request->__user->id;
        $message->creater_user_name = $request->__user->true_name;
        $message->created_at = $this->dt;
        return $message->save();
    }

    /**
     * 按角色消息写入
     * @param $request
     * @param $role_id
     * @param $url
     * @param $content
     * @return mixed
     */
    protected function sys_message_role(
        $request,
        $order_id,
        $role_id,
        $url,
        $content,
        $canton_fdn,
        $reshome_id = 0,
        $org_id = 0,
        $dept_fdn = '',
        $group_fdn = ''
    ) {
        return $this->sys_message_add(
            $request,
            $order_id,
            0,
            $role_id,
            $url,
            $content,
            $canton_fdn,
            $reshome_id,
            $org_id,
            $dept_fdn,
            $group_fdn
        );
    }

    protected function month_array($request, $start = null, $end = null)
    {
        $month_start = date('Y-01', time());
        if (isset($request['month_start'])) {
            $month_start = $request['month_start'];
        }
        if (!empty($start)) {
            $month_start = $start;
        }

        $month_end = date('Y-m', time());
        if (isset($request['month_end'])) {
            $month_end = $request['month_end'];
        }
        if (!empty($end)) {
            $month_end = $end;
        }

        $result = [];
        $ToStartMonth = strtotime($month_start); //转换一下
        $ToEndMonth = strtotime($month_end); //一样转换一下
        $i = false; //开始标示
        while ($ToStartMonth <= $ToEndMonth) {
            $NewMonth = !$i ? date('Y-m', strtotime('+0 Month', $ToStartMonth)) : date('Y-m', strtotime('+1 Month', $ToStartMonth));
            $ToStartMonth = strtotime($NewMonth);
            $i = true;
            $result[] = ['month' => $NewMonth];
        }
        array_pop($result);
        $month_start = $month_start . '-01';
        $month_end = date('Y-m-01', strtotime('+1 Month', $ToEndMonth));
        return [
            'result' => $result,
            'month_start' => $month_start,
            'month_end' => $month_end,
        ];
    }

    /**
     * 请求类
     */
    protected function requestAHYDCommitData($action, $params)
    {
        $res = http_post_iot($action, $params);
        $result = null;
        if ($res['code'] != '00000') {
            return ['code' => $res['code'], 'result' => ''];
        }

        $body = $res['body'];
        if (is_string($body)) {
            $body = json_decode($body, true);
        }

        $result = [];

        Log::info(json_encode($body));

        switch ($action) {
            case 'stopRecoverCard':
                $result = $body['oper_desc'];
                break;
        }
        return ['code' => $res['code'], 'result' => $body['oper_desc']];
        // return $result;
    }

}
