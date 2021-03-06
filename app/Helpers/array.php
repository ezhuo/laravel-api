<?php

if (!function_exists("array_column")) {

    function array_column($array, $column_name) {
        return array_map(function ($element) use ($column_name) {
            return $element[$column_name];
        }, $array);

    }
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
        $tt = is_object($tt) ? object2array($tt) : $tt;
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
    $tmp = [];
    foreach ($arr as $k => $v) {
        if (check_empty($result, $v->type)) {
            $result[$v->type] = [];
        }
        $tmp = ['label' => $v->name, 'value' => $v->code];
        if (isset($v->days)) {
            $tmp['days'] = $v->days;
        }
        $result[$v->type][] = $tmp;
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

/**
 * 在图上填充数据
 */
function arrayFillByChart($src, $start, $end, $format, $unit) {
    $src = is_object($src) ? object2array($src) : $src;
    $idx = $start;
    $at = [];
    while ($idx <= $end) {
        $at[] = $idx;
        $idx = date($format, strtotime('+1 ' . $unit, strtotime($idx)));
    }

    $result = [];

    foreach ($at as $val) {
        $dd = arrayFindByChart($src, $val);
        if (empty($dd)) {
            $dd = ['x' => $val, 'y' => 0];
        }
        $result[] = $dd;
    }
    return $result;
}

function arrayFindByChart($src, $k) {
    foreach ($src as $val) {
        if ($val['x'] == $k) {
            return $val;
        }
    }
    return null;
}

/**
 * 获取两个日期之间的所有日期
 * @param $startdate
 * @param $enddate
 * @return array
 */
function getDateFromRange($startdate, $enddate) {

    $stimestamp = strtotime($startdate);
    $etimestamp = strtotime($enddate);

    // 计算日期段内有多少天
    $days = ($etimestamp - $stimestamp) / 86400 + 1;

    // 保存每天日期
    $date = array();

    for ($i = 0; $i < $days; $i++) {
        $date[] = date('Y-m-d', $stimestamp + (86400 * $i));
    }

    return $date;
}

function getWeekDateFirstAndEnd($weekDate = null) {
    if (empty($weekDate)) $weekDate = date('Y-m-d');
    $sdefaultDate = $weekDate;

    //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
    $first = 1;
    //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
    $w = date('w', strtotime($sdefaultDate));
    //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
    $week_start = date('Y-m-d', strtotime("$sdefaultDate -" . ($w ? $w - $first : 6) . ' days'));
    //本周结束日期
    $week_end = date('Y-m-d', strtotime("$week_start +6 days"));

    return [
        'start' => $week_start,
        'end' => $week_end
    ];
}

function getWeekDateByYearWeek($week_at, $first = 0) {
    $tmp = explode('-', $week_at);
    $year = $tmp[0];
    $weeknum = $tmp[1];
    $firstdayofyear = mktime(0, 0, 0, 1, 1, $year);
    $firstweekday = date('N', $firstdayofyear);
    $firstweenum = date('W', $firstdayofyear);
    if ($firstweenum == 1) {
        $day = (1 - ($firstweekday - 1)) + 7 * ($weeknum - 1);
        $startdate = date('Y-m-d', mktime(0, 0, 0, 1, $day, $year));
        $enddate = date('Y-m-d', mktime(0, 0, 0, 1, $day + 6, $year));
    } else {
        $day = (9 - $firstweekday) + 7 * ($weeknum - 1);
        $startdate = date('Y-m-d', mktime(0, 0, 0, 1, $day, $year));
        $enddate = date('Y-m-d', mktime(0, 0, 0, 1, $day + 6, $year));
    }
    $startdate = date('Y-m-d', strtotime("{$first} day", strtotime($startdate)));
    $enddate = date('Y-m-d', strtotime("{$first} day", strtotime($enddate)));
    return array('start' => $startdate, 'end' => $enddate);
}