<?php

namespace App\Http\Controllers\WeiXin;

use Illuminate\Http\Request;
use App\Models\Frame\Base;

class WxTestController extends AppWxController {

    public function __construct(Request $request, Base $model) {
        parent::__construct($request, $model);
        $this->middleware('auth');
    }

    public function index1(Request $request) {
        $str = "[{\"name\":\"天\",\"code\":\"demo1\",\"list\":[{\"name\":\"枯藤\",\"value\":\"1\"},{\"name\":\"老树\",\"value\":\"2\"},{\"name\":\"昏鸦\",\"value\":\"3\"}]},{\"name\":\"净\",\"code\":\"demo2\",\"list\":[{\"name\":\"小桥\",\"value\":\"1\"},{\"name\":\"流水\",\"value\":\"2\"},{\"name\":\"人家\",\"value\":\"3\"}]},{\"name\":\"沙\",\"code\":\"demo3\",\"list\":[{\"name\":\"古道\",\"value\":\"1\"},{\"name\":\"西风\",\"value\":\"2\"},{\"name\":\"瘦马\",\"value\":\"3\"}]},{\"name\":\"秋思\",\"code\":\"demo4\",\"list\":[{\"name\":\"夕阳西下\",\"value\":\"1\"},{\"name\":\"断肠人\",\"value\":\"2\"},{\"name\":\"在天涯\",\"value\":\"3\"}]}]";
        return return_json(json_decode($str), 'success', HTTP_OK);
    }

    public function index2(Request $request) {
        return return_json([
            ['title' => '这是一个不寻常的故事'],
            ['title' => '这里突然出现了3句话'],
            ['title' => '是的，没错'],
            ['title' => '你可能猜到'],
            ['title' => '我就是在瞎诌'],
        ], 'success', HTTP_OK);
    }
}
