<?php

if (!function_exists('config_path')) {
    /**
     * Get the configuration path.
     * @param  string $path
     * @return string
     */
    function config_path($path = '') {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

if (!function_exists("array_column")) {

    function array_column($array, $column_name) {
        return array_map(function ($element) use ($column_name) {
            return $element[$column_name];
        }, $array);

    }
}

function get_dt() {
    return (new \DateTime("now"))
        ->setTimezone(new \DateTimeZone('PRC'))
        ->format('Y-m-d H:i:s');
}

/*
 * 把一个大于0的整数和，转换为二进制各位表达数的数组
 * 比如 30 = 16 + 8 + 4
 *   输入 30
 *   输出 [4，8，16]
 */
function sum2array($d) {
    $a = array();
    $m = 1;
    $chars = str_split(decbin($d));
    foreach (array_reverse($chars) as $char) {
        if ($char == '1')
            $a[] = $m;
        $m = $m * 2;
    }
    return $a;
}

/**
 * 将数组转换成树
 * @param array $sourceArr 要转换的数组
 * @param string $key 数组中确认父子的key，例子中为“id”
 * @param string $parentKey 数组中父key，例子中为“parentId”
 * @param type $childrenKey 要在树节点上索引子节点的key，例子中为“children”
 * @return array 返回生成的树
 */
function arrayToTree($sourceArr, $key, $parentKey, $childrenKey) {
    $tempSrcArr = array();

    $allRoot = TRUE;
    foreach ($sourceArr as $v) {
        $isLeaf = TRUE;
        foreach ($sourceArr as $cv) {
            if (($v[$key]) != $cv[$key]) {
                if ($v[$key] == $cv[$parentKey]) {
                    $isLeaf = FALSE;
                }
                if ($v[$parentKey] == $cv[$key]) {
                    $allRoot = FALSE;
                }
            }
        }
        if ($isLeaf) {
            $leafArr[$v[$key]] = $v;
        }
        $tempSrcArr[$v[$key]] = $v;
    }
    if ($allRoot) {
        return $tempSrcArr;
    } else {
        unset($v, $cv, $sourceArr, $isLeaf);
        foreach ($leafArr as $v) {
            if (isset($tempSrcArr[$v[$parentKey]])) {
                $tempSrcArr[$v[$parentKey]][$childrenKey] = (isset($tempSrcArr[$v[$parentKey]][$childrenKey]) && is_array($tempSrcArr[$v[$parentKey]][$childrenKey])) ? $tempSrcArr[$v[$parentKey]][$childrenKey] : array();
                array_push($tempSrcArr[$v[$parentKey]][$childrenKey], $v);
                unset($tempSrcArr[$v[$key]]);
            }
        }
        unset($v);
        return arrayToTree($tempSrcArr, $key, $parentKey, $childrenKey);
    }
}


function object2array($object) {
    return @json_decode(@json_encode($object), 1);
}

function isEmpty($obj, $val) {
    return empty($obj) ? $val : $obj;
}

function isNotEmpty($obj, $val) {
    return !empty($obj) ? $val : $obj;
}

function isExists($is, $val) {
    return $is ? $val : null;
}

function isImage($filename) {
    $types = '.gif|.jpeg|.jpg|.png|.bmp';//定义检查的图片类型
    if (file_exists($filename)) {
        $info = getimagesize($filename);
        $ext = image_type_to_extension($info['2']);
        return stripos($types, $ext);
    } else {
        return false;
    }
}

/**
 * 检查是数组是否为空, 空=true 不空=false
 * @param $obj
 * @param $val
 * @return bool
 */
function check_empty($obj, $val) {
    if (!isset($obj)) {
        return true;
    }
    if (is_array($obj) || (is_object($obj) && get_class($obj) == 'Illuminate\Http\Request')) {
        if (!isset($obj[$val])) {
            return true;
        }
        if (isset($obj[$val]) && empty($obj[$val])) {
            return true;
        }
    } else if (is_object($obj)) {
        if (!isset($obj->{$val})) {
            return true;
        }
        if (isset($obj->{$val}) && empty($obj->{$val})) {
            return true;
        }
    }
    return false;
}

function check_not_empty($obj, $val) {
    return !check_empty($obj, $val);
//    return isset($obj[$val]);
}

/**
 * 将数据库返回数组转换为valChange类型数组
 * 即数组列转行
 * 1 2 3 4
 * 5 6 7 8
 * 变换为 array(1=>array(2=>array(3=>array(4))))
 */
function arrayToArray($tV) {
    $rv = array();
    $tV = is_object($tV) ? object2array($tV) : $tV;
    if (empty($tV) || !is_array($tV)) {
        return $rv;
    }
    $keyArr = is_object($tV[0]) ? object2array($tV[0]) : $tV[0];
    $keys = array_keys($keyArr);
    $valStr = $keys[sizeof($keys) - 1];
    unset($keys[sizeof($keys) - 1]);
    $keyStr = "[\$tt['" . implode("']][\$tt['", $keys) . "']]";
    $vvv = sprintf("\$rv%s=\$tt['%s'];", $keyStr, $valStr);
    foreach ($tV as $tt) {
        eval($vvv);
    }
    unset($vvv);
    return $rv;
}

function ObjectToArrayToArray($tV) {
    return arrayToArray(object2array($tV));
}

function ObjectToArray2($tV, $key_name, $val_name) {
    if (is_object($tV)) $tV = object2array($tV);
    return array_column($tV, $val_name, $key_name);
}

/**
 * 将数组第一个节点为KEY,其它为VALUE
 * @param [type] $tV [description]
 */
function FirstToArray($tV) {
    $rv = array();
    if (empty($tV) || !is_array($tV)) {
        return $rv;
    }
    foreach ($tV as $value) {
        $rv[reset($value)] = array_splice($value, 1);
    }
    return $rv;
}

function arrayToArrayByType($arr) {
    $result = [];
    foreach ($arr as $k => $v) {
        if (check_empty($result, $v->type)) {
            $result[$v->type] = [];
        }
        $result[$v->type][] = ['key' => $v->code, 'val' => $v->name];
    }
    return $result;
}

/*
* 将excel转换为数组 by aibhsc
* */
function format_excel2array($filePath = '', $sheet = 0) {
    require('includes/PHPExcel.php');//引入PHP EXCEL类
    if (empty($filePath) or !file_exists($filePath)) {
        die('file not exists');
    }
    $PHPReader = new PHPExcel_Reader_Excel2007();        //建立reader对象
    if (!$PHPReader->canRead($filePath)) {
        $PHPReader = new PHPExcel_Reader_Excel5();
        if (!$PHPReader->canRead($filePath)) {
            echo 'no Excel';
            return;
        }
    }
    $PHPExcel = $PHPReader->load($filePath);        //建立excel对象
    $currentSheet = $PHPExcel->getSheet($sheet);        //**读取excel文件中的指定工作表*/
    $allColumn = $currentSheet->getHighestColumn();        //**取得最大的列号*/
    $allRow = $currentSheet->getHighestRow();        //**取得一共有多少行*/
    $data = array();
    for ($rowIndex = 1; $rowIndex <= $allRow; $rowIndex++) {        //循环读取每个单元格的内容。注意行从1开始，列从A开始
        for ($colIndex = 'A'; $colIndex <= $allColumn; $colIndex++) {
            $addr = $colIndex . $rowIndex;
            $cell = $currentSheet->getCell($addr)->getValue();
            if ($cell instanceof PHPExcel_RichText) { //富文本转换字符串
                $cell = $cell->__toString();
            }
            $data[$rowIndex][$colIndex] = $cell;
        }
    }
    return $data;
}

function parse_flog($str, $file) {
    $file = empty($file) ? date("Y-m-d") : $file;
    $fp_tmp = fopen("/tmp/" . $file . ".txt", "a+");
    fputs($fp_tmp, date('Y-m-d H:i:s') . "：\n" . $str . "\n");
    fclose($fp_tmp);
}

/**
 * 格式化时间
 * @param $min
 */
function format_minute_cn($min) {
    $old_times = $min;
    $min = abs($min);
    if ($min > 59) {
        $times_js = $min;
        $min = floor($times_js / 60) . '时';
        if (($times_js % 60) > 0) {
            $min .= ($times_js % 60) . '分';
        }
    } elseif ($min > 0) {
        $min = $min . '分';
    }
    return ($old_times >= 0 ? $min : '-' . $min);
}

function date_add2($dt, $cnt, $unit) {
//    dd("+" . $cnt . " " . $unit);
    return date('Y-m-d H:i:s', strtotime("+" . $cnt . " " . $unit, strtotime($dt)));
}

function get_filename_bybrowser($filename) {
    $my_broswer = $_SERVER['HTTP_USER_AGENT'];
    if (!preg_match("/Firefo|Chrome|Opera|Safari/", $my_broswer)) {
        $filename = urlencode($filename);
        $filename = str_replace("+", "%20", $filename);
        //      $filename=iconv('utf-8', 'gbk', $filename);//防止文件名存储时乱码
    } else {
        $filename = sprintf("\"%s\"", $filename);
    }
    return $filename;
}

function get_dict_name($dict, $key) {
    $result = [];
    $keys = explode(',', $key);
    foreach ($keys as $id) {
        if (!empty($id) && isset($dict[$id])) {
            $result[] = $dict[$id];
        }
    }
    return implode(',', $result);
}