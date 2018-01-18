<?php

namespace App\Http\Controllers\Test;

use App\Models\Frame\Base;
use Illuminate\Http\Request;
use App\Http\Controllers\Frame\AppDataController;

class IndexController extends AppDataController {

    public function __construct(Request $request, Base $model) {
        return parent::__construct($request, $model);
    }

    public function index(Request $request) {
        return 'hello api';
    }

    public function err_404(Request $request) {
        return abort(404);
    }

    public function export(Request $request) {
        $data = [
            [
                'org_corpname' => '系统模板',
                'org_name' => 'sys'
            ],
            [
                'org_corpname' => '系统模板2',
                'org_name' => 'sys2'
            ]
        ];
        $exportname = "fdsafd.xls";
        return response()
            ->view('export.export', [
                'title' => 'James',
                'field' => [
                    'org_name' => ['title' => '企业名称', 'width' => 150],
                    'org_corpname' => ['title' => '公司全称', 'width' => 150],
                ],
                'data' => $data
            ])
            ->header('Pragma', 'no-cache')
            ->header('Content-type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment;filename=' . $exportname);

    }


}