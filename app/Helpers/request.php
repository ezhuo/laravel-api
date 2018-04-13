<?php

// 得到request参数
function getRequestParams($request, $key_array) {
    $data = [];
    foreach ($key_array as $key) {
        if (gettype($request[$key]) != NULL && $request[$key] != '') {
            $data[$key] = trim($request[$key]);
        }
    }
    return $data;
}

function getRequestWhere($request, $fields) {
    $where = [];
//    dd($fields);
    foreach ($fields as $v) {
        if (check_not_empty($request, $v)) {
            $where[$v] = $request[$v];
        }
    }
    return $where;
}

//使用原始库 优点 控制时间 不受环境影响比如发短信提交后就直接结束
function make_request($url, $params, $type = 'GET') {
    foreach ($params as $key => &$val) {
        if (is_array($val)) $val = implode(',', $val);
        $post_params[] = $key . '=' . urlencode($val);
    }
    $post_string = implode('&', $post_params);
    $parts = parse_url($url);
    $fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, 30);
    if ('GET' == $type) $parts['path'] .= '?' . $post_string;
    $out = "$type " . $parts['path'] . " HTTP/1.1\r\n";
    $out .= "Host: " . $parts['host'] . "\r\n";
    $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $out .= "Content-Length: " . strlen($post_string) . "\r\n";
    $out .= "Connection: Close\r\n\r\n";
    // Data goes in the request body for a POST request
    if ('POST' == $type && isset($post_string)) $out .= $post_string;
    fwrite($fp, $out);
    fclose($fp);
}

function make_post($url, $params, $type = 'POST') {
    $line = "";
    foreach ($params as $key => $val) {
        if (is_array($val)) $val = implode(',', $val);
        $post_params[] = $key . '=' . urlencode($val);
    }
    $post_string = implode('&', $post_params);
    $parts = parse_url($url);
    $fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, 30);
    if ('GET' == $type) $parts['path'] .= '?' . $post_string;
    $out = "$type " . $parts['path'] . " HTTP/1.1\r\n";
    $out .= "Host: " . $parts['host'] . "\r\n";
    if (isset($params['sid']))
        $out .= "Cookie: PHPSESSID=" . $params['sid'] . "; path=/;\r\n";
    $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $out .= "Content-Length: " . strlen($post_string) . "\r\n";
    $out .= "Connection: Close\r\n\r\n";
    // Data goes in the request body for a POST request
    if ('POST' == $type && isset($post_string)) $out .= $post_string;
    fwrite($fp, $out);
    while (!feof($fp)) $line .= fread($fp, 4096);
    fclose($fp);
    $pos = strpos($line, "\r\n\r\n");
    $head = substr($line, 0, $pos);    //http head
    $status = substr($head, 0, strpos($head, "\r\n"));    //http status line
    $body = substr($line, $pos + 4, strlen($line) - ($pos + 4));//page body
    //$body=  str_replace("&lt;","<",$body);
    //$body=  str_replace("&gt;",">",$body);
    Log::info($body);
    return trim($body);
}

function get_url_content($url, $params = [], $type = 'POST') {
    Log::info($url);
    $strCookie = isset($params['sid']) ? "PHPSESSID=" . $params['sid'] : "";
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20); //单位：秒 超时时间
        curl_setopt($ch, CURLOPT_HEADER, 0);//array('Content-type: text/json')
        curl_setopt($ch, CURLOPT_COOKIE, $strCookie);
        switch ($type) {
            case "GET" :
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                break;
            case "POST":
                curl_setopt($ch, CURLOPT_POST, true);
                break;
            case "PUT" :
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                break;
            case "DELETE":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $file_contents = curl_exec($ch);
        curl_close($ch);
    } else {
        $context = array();
        $context['http'] = array(
            'timeout' => 60,
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n" . "Cookie: PHPSESSID=" . $params['sid'] . ";\r\n",
            'content' => http_build_query($params, '', '&'),
        );
        $file_contents = file_get_contents($url, 0, stream_context_create($context));
    }
    //$file_contents=  str_replace("&lt;","<",$file_contents);
    //$file_contents=  str_replace("&gt;",">",$file_contents);
//    Log::info($file_contents);
    return trim($file_contents);
}

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

function Output($fileContent, $showname) {
    $downloadType = "attachment";//inline内嵌显示//attachment 下载显示
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    header("Content-Disposition: $downloadType; filename=" . $showname);
    header('Content-Encoding: none');
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: " . strlen($fileContent));
    echo($fileContent);
    exit;
}


/**
 * 推送
 * @param $tag
 * @param $msg
 * @return \JPush\Model\PushResponse
 */
function pushMsg_JPush($tag, $msg) {
    $key = env('PUSH_APP_Key');
    if (empty($secret)) $key = DEFAULT_PUSH_APP_Key;
    $secret = env('PUSH_APP_Secret');
    if (empty($secret)) $secret = DEFAULT_PUSH_APP_Secret;

    $client = new JPush\JPushClient($key, $secret);

    $result = $client->push()
        ->setPlatform(JPush\Model\Platform('android', 'ios'))
        ->setAudience(JPush\Model\alias($tag))
        //->setAudience(JPush\Model\all)
        ->setNotification(JPush\Model\notification('JPush',
            JPush\Model\android($msg['content'], $msg['title'], 1, $msg),
            JPush\Model\ios($msg['content'], $msg['title'], "+1", true, $msg, "Ios6")
        ))
        //->setMessage(JPush\Model\message($msg['content'],$msg['title'],$msg['action'],$msg))
        //->printJSON()
        ->send();
    return $result;
}

/**
 * 获取分页
 */
function request_page($request) {
    $result = [];
    if (check_not_empty($request, 'page')) {
        $page = intval($request['page']);
        $page = $page - 1;
        $pageSize = DB_PAGE_SIZE;
        if (check_not_empty($request, 'pageSize'))
            $pageSize = intval($request['pageSize']);

        $page = ($page < 0 ? 0 : $page) * $pageSize;
        $result['page'] = $page;
        $result['size'] = $pageSize;
    } else {
        $result['page'] = 0;
        $result['size'] = APP_PERPAGE;
    }
    return $result;
}