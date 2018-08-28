<?php

namespace App\Models\Scope;

use DB;
use App\Models\Frame\Data;

class OrgInfo extends Data
{
    protected $table = DB_PREFIX . 'org_info';
    protected $primaryKey = 'org_id';
    protected $dict_value = 'org_corpname';

    protected $fillable = [
        'org_id', 'parent_id', 'fdn', 'org_name', 'org_corpname', 'linkman', 'phone', 'tel', 'email', 'address', 'canton_id', 'canton_fdn', 'logo', 'expire_time', 'memo', 'orderby', 'qrcode', 'files', 'status', 'created_at', 'updated_at', 'deleted_at'
    ];

    protected $export_field = [
        'org_name' => ['title' => '企业名称', 'width' => 150],
        'org_corpname' => ['title' => '公司全称', 'width' => 150],
        'linkman' => ['title' => '联系人', 'width' => 150],
        'phone' => ['title' => '手机号', 'width' => 100],
        'tel' => ['title' => '座机', 'width' => 100],
        'email' => ['title' => '邮箱', 'width' => 100],
        'canton_text_name' => ['title' => '所属区域', 'width' => 150],
        'address' => ['title' => '地址', 'width' => 150],
        'memo' => ['title' => '备注', 'width' => 150],
    ];

    protected $rules_setting = [
        'rules' => [
            'org_name' => 'bail|required|min:2|unique',
            'org_corpname' => 'bail|required|min:2|unique',
        ],
        'field' => [
            'org_name' => '机构简称',
            'org_corpname' => '机构全称',
        ]
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        return;
    }

    public function org_init($request, $org_id)
    {
        if (empty($org_id)) {
            return false;
        }
        $sql = sprintf("call pro_org_init(%s , %s);", $org_id, $request->__user->id);
        return DB::select($sql);
        // return true;
    }

    public static function get_name_byid($id)
    {
        $obj = OrgInfo::select(['org_name', 'org_corpname'])->find($id);
        if ($obj) {
            return $obj->org_name;
        }
        return null;
    }
}
