<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Frame\AppDataController;
use App\Models\Frame\Base;
use Captcha;
use Illuminate\Http\Request;

class IndexController extends AppDataController
{

    public function __construct(Request $request, Base $model)
    {
        return parent::__construct($request, $model);
    }

    public function index(Request $request)
    {
        return 'hello api';
    }

    public function err_404(Request $request)
    {
        return abort(404);
    }

    public function export(Request $request)
    {
        $data = [
            [
                'org_corpname' => '系统模板',
                'org_name' => 'sys',
            ],
            [
                'org_corpname' => '系统模板2',
                'org_name' => 'sys2',
            ],
        ];
        $exportname = "fdsafd.xls";
        return response()
            ->view('export.export', [
                'title' => 'James',
                'field' => [
                    'org_name' => ['title' => '企业名称', 'width' => 150],
                    'org_corpname' => ['title' => '公司全称', 'width' => 150],
                ],
                'data' => $data,
            ])
            ->header('Pragma', 'no-cache')
            ->header('Content-type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment;filename=' . $exportname);

    }

    public function userStatusQuery(Request $request)
    {
//        $res = http_post_iot('userStatusQuery', ['msisdn' => '1440167311753']);
        //        $res = http_post_iot('onlineGPRSRealQuery', ['msisdn' => '1440167311753']);
        //        $res = http_post_iot('onAndOffInfoQuery', ['msisdn' => '1440167311753']);
        //        $res = http_post_iot('userPkgListQuery', ['msisdn' => '1440167311753']);
        $res = http_post_iot('balanceRealSingle', ['card_info' => ['1440167311753', '1440167312150'], 'type' => 0]);
        dd($res);
    }

    public function captcha(Request $request)
    {
        return Captcha::create('default', true);
    }

    public function check(Request $request)
    {
        $code = $request['code'];
        $key = $request['key'];
        $bool = Captcha::check_api($code, $key);
        dd($bool);
    }

    public function captcha2(Request $request)
    {
        $builder = new CaptchaBuilder;
        $builder->build();
        // dd($builder->getPhrase());
        header("Cache-Control: no-cache, must-revalidate");
        header('Content-Type: image/png');
        $builder->output();
    }
}
