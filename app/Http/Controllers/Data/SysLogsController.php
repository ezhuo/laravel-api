<?php

namespace App\Http\Controllers\Data;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Data\SysLogs;
use App\Http\Controllers\Frame\AppDataController;

class SysLogsController extends AppDataController {

    public function __construct(Request $request, SysLogs $model) {
        parent::__construct($request, $model);
        $this->middleware('auth');
    }

    public function get_fields($request, $dataset) {
        return [
            "id",
            "title",
            "content",
            "other_info",
            "api",
            "no_auth",
            "ip",
            DB::raw("case source when 'sys' then '监管平台' when 'org' then '评估平台' end as source"),
            DB::raw("case monitor when 'pc' then 'PC端' when 'mobi' then '移动端' end as monitor"),
            "org_fdn",
            "org_name",
            "creater_user_id",
            "creater_user_name",
            "created_at"
        ];
    }

    public function store(Request $request) {
        $title = $request['title'];
        $content = $request['content'];
        $res = SysLogs::write($request, $title, $content);
        return return_json([], null, ($res ? HTTP_OK : HTTP_WRONG));
    }

}