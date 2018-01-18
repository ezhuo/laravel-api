<?php

namespace App\Models\Data;

use App\Models\Frame\Data;
use Log;
use Illuminate\Support\Facades\Request;

class SysLogs extends Data {

    protected $table = DB_PREFIX . 'base_operation_log';
    protected $fillable = [
        "title",
        "content",
        "other_info",
        "api",
        "no_auth",
        "ip",
        "source",
        "monitor",
        "org_fdn",
        "org_name",
        "creater_user_id",
        "creater_user_name",
        "created_at"
    ];

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        return;
    }

    public static function write($request, $title, $content = '进入') {
        $model = new SysLogs();
        $model->title = $title;
        $model->content = $content;
        $model->other_info = $request['other_info'];

        $model->api = (method_exists($request, 'getPathInfo') ? $request->getPathInfo() : "");
        $model->no_auth = 0;
        $ip = "--";
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        };

        $request->setTrustedProxies(['127.0.0.1', '0.0.0.1', '::1']);
        $ip = $request->getClientIp();
        $model->ip = $ip;

//        Log::info('ip: ' . $ip);

        $model->source = $request->__source;
        $model->monitor = $request->__monitor;

        if ($request->__source == 'org') {
            $model->org_fdn = $request['org_fdn'];
            $model->org_name = $request['org_corpname'];
        } else if ($request->__source == 'sys') {
            $model->canton_fdn = $request->__user->canton_fdn;
        }

        $model->creater_user_id = $request->__user->id;
        $model->creater_user_name = $request->__user->true_name;
        $model->created_at = get_dt();

        return $model->save();
    }

}
