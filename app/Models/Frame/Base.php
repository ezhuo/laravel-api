<?php
namespace App\Models\Frame;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use DB;

class Base extends Model {

    /**
     * 设置是否自动更新时间字段
     * @var bool
     */
    public $timestamps = false;

    /**
     * 当前数据表
     * @var string
     */
    protected $table = DB_PREFIX;

    /**
     * 表别名
     * @var null
     */
    protected $tableAlias = null;

//    protected $hidden = ['created_at', 'updated_at'];

    /**
     * 允许批量添加字段
     * @var array
     */
    protected $fillable = [];

    /**
     * 分页
     * @var int
     */
    protected $perPage = 15;

    /**
     * 当前表的主键
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * 是否自增长
     * @var bool
     */
    public $incrementing = true;

    /**
     * 主键的类型
     * @var string
     */
    protected $keyType = 'int'; //'string';

    /**
     * 主键是否是UUID
     * @var bool
     */
    protected $primaryIsUUID = false;


    //数据字典KEY
    protected $dict_key = '';

    //数据字典VALUE
    protected $dict_value = '';

    /**
     * 排序
     * @var array
     */
    protected $order_field = [];

    /**
     * 导出设置
     * protected $export_field = [
     * 'org_id' => ['title' => '履职人员', 'width' => 150],
     * ];
     */
    protected $export_field = [];

    /*
        *  验证规则 eq=为相等 neq=不相等
            protected $rules_setting = [
                'rules' => [
                    'phone' => 'bail|required|min:1|unique',
                    'address' => [
                        'bail', 'required', 'min:2', 'unique' => ['org_id' => '2'],
                    ],
                    'contact_person' => [
                        'unique' => ['eq' => ['org_id' => 1, 'status' => 1], 'neq' => ['org_id' => 2, 'status' => 2]],
                    ]
                ],
                 'field' => [
                    'phone' => '电话号码',
                    'address' => '地址',
                    'contact_person' => '联系人'
                  ]
            ];
        */

    protected $rules_setting = [
        "rules" => [],
        "field" => []
    ];

    protected $rules_message = [
        'required' => ':attribute 为必填项；',
        'unique' => ':attribute 数据已存在，请不要输入重复数据；',

        'min' => ':attribute 最少为 :min 个字符；',
        'max' => ':attribute 最多为 :max 个字符；',

        'string' => ':attribute 格式不正确，请输入字符串；',
        'ip' => ':attribute 格式不正确，请输入合法的IP地址；',

        'numeric' => ':attribute 必须为数字.',
    ];

    /**
     * 构造函数
     * Base constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        return;
    }

    /**
     * 获取验证规则
     * @return array
     */
    public function get_rules_setting() {
        $this->rules_setting['message'] = $this->rules_message;
        return $this->rules_setting;
    }

    /**
     * 设置验证规则
     * @param $new
     * @return mixed
     */
    public function set_rules_setting($new) {
        return $this->rules_setting = $new;
    }

    /**
     * 删除要验证的字段
     * @param $field
     * @return bool
     */
    public function delete_rules_setting($field) {
        if (empty($field)) return false;
        if (array_key_exists('rules', $this->rules_setting['rules'])) {
            if (array_key_exists($field, $this->rules_setting['rules'])) {
                unset($this->rules_setting['rules'][$field]);
            }
        }
    }

    /**
     * 获取允许导出的数据表字段
     * @return array
     */
    public function get_export_fields() {
        $field = [];
        foreach ($this->export_field as $k => $v) {
            if (!array_key_exists('width', $v)) {
                $v['width'] = 150;
            }
            $field[$k] = $v;
        }
        return $field;
    }

    /**
     * 获取表别表
     * @return null
     */
    public function get_table_alias() {
        return $this->tableAlias;
    }

    /**
     * 获取数据表字段
     * @param $table
     * @return mixed
     */
    public static function get_table_fields($table) {
        $cache_file = __FUNCTION__ . "_" . $table;
        $columns = S($cache_file);
        if (empty($columns)) {
            $columns = Schema::getColumnListing($table);
            S($cache_file, $columns);
        }
        return $columns;
    }

    /**
     * 获取排序字段
     */
    public function get_order_field() {
        $result = $this->order_field;
        if (empty($result)) {
            $result[$this->primaryKey] = 'desc';
        }
        return $result;
    }

    /**
     * 获取数据字典中的KEY
     */
    public function get_dict_key() {
        if (empty($this->dict_key)) {
            $this->dict_key = $this->primaryKey;
        }
        return $this->dict_key;
    }

    /**
     * 获取数据字典中的value
     */
    public function get_dict_value() {
        return $this->dict_value;
    }

    /**
     * 判断主键是否是UUID
     * @return bool
     */
    public function get_primary_is_uuid() {
        return $this->primaryIsUUID;
    }
}
