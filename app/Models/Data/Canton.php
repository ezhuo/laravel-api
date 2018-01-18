<?php

namespace App\Models\Data;

use Illuminate\Support\Facades\DB;
use App\Models\Frame\Data;

class Canton extends Data {
    protected $table = DB_PREFIX . 'sys_canton';
    protected $hidden = ['create_time'];
    protected $primaryKey = 'canton_id';
    protected $keyType = 'string';

    protected $fillable = [
        "canton_id",
        "name",
        "parent_id",
        "ordernum",
        "layer",
        "fdn",
        "canton_uniqueno",
        "text_name",
        "is_del",
        "creater_user_id",
        "create_time"
    ];

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        return;
    }

    public function canton_data($request) {
        $cur_fdn = APP_CANTON_FDN;
        $cur_id = APP_CANTON_ID;

        if ($request->__user->canton_fdn) {
            $cur_fdn = $request->__user->canton_fdn;
            $tmp = explode('.', $cur_fdn);
            $cur_id = intval($tmp[count($tmp) - 2]);
        }
        $cache_key = __FUNCTION__ . $cur_fdn;
        $result = S($cache_key);
        if (intval($cur_id) == 3520)
            $parent_id = '0000000';
        else {
            $obj = Canton::where(["canton_id" => $cur_id])->first(['parent_id']);
            if (!empty($obj)) {
                $parent_id = $obj->parent_id;
            }
        }
        if (empty($result)) {
            $result = Canton::where('fdn', 'like', $cur_fdn . '%')
                ->select([
                    DB::raw("lpad(cast(canton_id as signed)," . APP_CANTON_LENGTH . ",0) as id"),
                    DB::raw("case when parent_id = '" . $parent_id . "' then '#' else lpad(cast(parent_id as signed)," . APP_CANTON_LENGTH . ",0) end as parent"),
                    DB::raw("name as text"), "fdn", "text_name"
                ])
                ->orderBy('fdn')
                ->get();
            S($cache_key, $result);
        }
        return $result;
    }

    public function canton_selectselectselect($request, $id) {
        $cache_key = __FUNCTION__ . $id;
        $data = S($cache_key);
        if (empty($data)) {
            $res = Canton::select([
                DB::raw("lpad(cast(canton_id AS signed)," . APP_CANTON_LENGTH . ",0) as canton_id"),
                DB::raw("lpad(cast(parent_id AS signed)," . APP_CANTON_LENGTH . ",0) as parent_id"),
                DB::raw("name as title"),
                DB::raw("fdn as val"),
                "text_name"
            ]);
            if (empty($id)) {
                $res = $res->whereRaw(" LENGTH(fdn) <= 32 and is_del = 0 ");
            } else {
                $res = $res->whereRaw(" parent_id = " . $id . " and is_del = 0 ");
            }
            $data = $res->orderBy('fdn')->get();
            S($cache_key, $data, 60 * 24 * 7);
        }
        return $data;
    }

    public static function get_name_byfdn($fdn) {
        $obj = Canton::where(['fdn' => $fdn])->first(['name', 'text_name']);
        if ($obj) {
            return $obj->name;
        }
        return null;
    }

    public static function get_id_byfdn($fdn) {
        $obj = Canton::where(['fdn' => $fdn])->first(['canton_id']);
        if ($obj) {
            return $obj->canton_id;
        }
        return 0;
    }

}
