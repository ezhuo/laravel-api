<?php

function return_json($data = [], $msg = 'success', $http_code = HTTP_OK) {
    $data = [
        'data' => $data,
        'message' => $msg,
        'dt' => get_dt()
    ];
    return response($data, $http_code);
}

function return_excel($path, $title, $field, $data, $dict, $filename) {
    return response()
        ->view($path, [
            'title' => $title,
            'field' => $field,
            'data' => $data,
            'dict' => $dict
        ])
        ->header('Pragma', 'no-cache')
        ->header('Content-type', 'application/vnd.ms-excel; charset=UTF-8')
        ->header('Content-Disposition', 'attachment;filename=' . $filename);
}

/**
 * 检查当前请求，是否在URL验证身份
 * @param $request
 */
function check_url_auth_list($request) {
    $current_action = $request->route()->getActionMethod();
    return (in_array($current_action, REQUEST_URL_AUTH_LIST));
}

/**
 * 检查当前请求，是否不验证是否合法
 * @param $request
 */
function check_no_auth_list($request) {
    $current_action = $request->route()->getActionMethod();
    return (in_array($current_action, REQUEST_NO_AUTH_LIST));
}

/**
 * @param $key
 * @param null $val
 * @param int $minutes 单位：分钟
 * @return mixed
 * @throws Exception
 */
function S($key, $val = null, $minutes = CACHE_EXPIRE) {
    if (empty($val)) {
        return cache($key, null);
    } else {
        return cache([$key => $val], $minutes);
    }
}

/**
 * 删除区域的缓存数据
 * @throws Exception
 */
function canton_cache_clear() {
    cache()->forget('canton_selectselectselect');
    cache()->forget('canton_data' . APP_CANTON_FDN);
    cache()->flush();
}

function get_db_uuid() {
    $result = "";
    $obj = DB::select(\DB::raw('select uuid() as uuid'));
    if (!empty($obj)) {
        $result = $obj[0]->uuid;
    }
    return $result;
}

function get_uuid_uniqid() {
    //strtoupper转换成全大写的
    $charid = strtoupper(md5(uniqid(mt_rand(), true)));
    $uuid = substr($charid, 0, 8) . substr($charid, 8, 4) . substr($charid, 12, 4) . substr($charid, 16, 4) . substr($charid, 20, 12);
    return $uuid;
}

function get_uuid() {
    $uuid = null;
    try {
        $obj = \Ramsey\Uuid\Uuid::uuid1(time());
        $uuid = $obj->getHex();
        $uuid = strtoupper(md5($uuid));
    } catch (\Ramsey\Uuid\Exception\UnsatisfiedDependencyException $e) {
        $uuid = get_uuid_uniqid();
    }
    return $uuid;
}

function getTree2($data, $pid = 0, $key = 'id', $pKey = 'parentId', $childKey = 'child', $maxDepth = 0) {
    static $depth = 0;
    $tree = array();
    $depth++;
    if (intval($maxDepth) <= 0) {
        $maxDepth = count($data) * count($data);
    }
    if ($depth > $maxDepth) {
        return $tree;//exit("error recursion:max recursion depth {$depth}{$maxDepth}");
    }

    foreach ($data as $rk => $rv) {


        if ($rv[$pKey] == $pid) {
            $rv[$childKey] = getTree2($data, $rv[$key], $key, $pKey, $childKey, $maxDepth);
            // $rv['count']=count($rv[$childKey]);
            if (count($rv[$childKey]) == 0) {
                $rv['is_select'] = 1;
            } else {
                $rv['is_select'] = 0;
            }

            $tree[] = $rv;

        }
    }
    return $tree;
}


function getTree($data, $pid = 0, $key = 'id', $pKey = 'parentId', $childKey = 'child', $maxDepth = 0) {
    static $depth = 0;
    $tree = array();
    $depth++;
    if (intval($maxDepth) <= 0) {
        $maxDepth = count($data) * count($data);
    }
    if ($depth > $maxDepth) {
        return $tree;//exit("error recursion:max recursion depth {$depth}{$maxDepth}");
    }

    foreach ($data as $rk => $rv) {
//     	echo "<pre>";print_r($pid);
//     	echo "<pre>";print_r($rv[$pKey]);exit;
        if ($rv[$pKey] == $pid) {
            $rv[$childKey] = getTree($data, $rv[$key], $key, $pKey, $childKey, $maxDepth);
            //if( count($rv[$childKey])==0 )    unset($rv[$childKey]);
            $tree[] = $rv;
        }
    }
    return $tree;
}

/**
 * 创建二维码
 * @param $tempName
 * @param string $text
 * @param $logo
 * @return bool
 */
function qrcode_create($tempName, $text = 'dxinfo', $logo) {
    if (empty($tempName)) return fasle;
    $info = pathinfo($tempName);
    if (empty($logo)) {
        $logo = UPLOAD_DIR . "/templates/logo.png";
    }

    if (!is_dir($info['dirname'])) mkdir($info['dirname']);
    //L(QR_ECLEVEL_L，7%)，M(QR_ECLEVEL_M，15%)，Q(QR_ECLEVEL_Q，25%)，H(QR_ECLEVEL_H，30%)。
    \PHPQRCode\QRcode::png($text, $tempName, 'H', 10, 2);
    if ($logo) {
        $QR = imagecreatefromstring(file_get_contents($tempName));
        $logo = imagecreatefromstring(file_get_contents($logo));
        $QR_width = imagesx($QR);//二维码图片宽度
        $QR_height = imagesy($QR);//二维码图片高度
        $logo_width = imagesx($logo);//logo图片宽度
        $logo_height = imagesy($logo);//logo图片高度
        $logo_qr_width = $QR_width / 5;
        $scale = $logo_width / $logo_qr_width;
        $logo_qr_height = $logo_height / $scale;
        $from_width = ($QR_width - $logo_qr_width) / 2;
        //重新组合图片并调整大小
        imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
            $logo_qr_height, $logo_width, $logo_height);
        @imagepng($QR, $tempName);//输出图片
    }
    return true;
}


function gEnv($key = '', $name = '.env') {
    $configPath = config_path() . DIRECTORY_SEPARATOR . $name . '.env';
    if (!\File::exists($configPath)) {
        return FALSE;
    }

    $data = collect(file($configPath, FILE_IGNORE_NEW_LINES));
    $data->transform(function ($item) {
        list($key, $value) = explode('=', $item);
        $list[$key] = $value;

        return $list;
    });

    $list = $data->toArray();
    foreach ($list as $value) {
        foreach ($value as $k => $v) {
            $arr[$k] = $v;
        }
    }
    if (!empty($key)) {
        return $arr[$key];
    }
    return $arr;
}

function modifyEnv(array $data) {
    $envPath = base_path() . DIRECTORY_SEPARATOR . '.env';
    $contentArray = collect(file($envPath, FILE_IGNORE_NEW_LINES));
    $contentArray->transform(function ($item) use ($data) {
        foreach ($data as $key => $value) {
            if (str_contains($item, $key)) {
                return $key . '=' . $value;
            }
        }
        return $item;
    });
    $content = implode($contentArray->toArray(), "\n");

    \File::put($envPath, $content);
}

function token_encode($arr) {
    return \Firebase\JWT\JWT::encode($arr, APP_TOKEN_CODE);
}

function token_decode($str) {
    return \Firebase\JWT\JWT::decode($str, APP_TOKEN_CODE, array('HS256'));
}

function password_encode($str) {
    return md5(md5($str . APP_PASSWORD_CODE));
}

//判断是否是Eloquent ORM
function is_orm($obj) {
    return (is_object($obj) && method_exists($obj, 'getTable'));
}

/**
 * 判断是否是Builder,当ORM执行过Where后，类型就会变为Builder
 * @param $obj
 * @return bool
 */
function is_builder($obj) {
    return (is_object($obj) && method_exists($obj, 'getModel'));
}