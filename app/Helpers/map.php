<?php


///address=>[lng] => 113.68827141439 [lat] => 34.779807706414
function getLatLongByBaiDu($address, $ak = '87c3e9435d10e6ad8a06622506836849') {

    $location = null;
    if (!$address) return $location;
//    $url = "http://api.map.baidu.com/geocoder/v2/?output=json&ak=$ak&address=" . $address;
    $url = "http://api.map.baidu.com/geocoder/v2/";
    $response = make_post($url, ['output' => 'json', 'ak' => $ak, 'address' => $address]);
//    $response = get_url_content($url);
    $response = json_decode($response);

    if (!$response) return $location;
    if ($response->status == 0)
        $location = (array)$response->result->location;
    return $location;
}

//[lng] => 113.68827141439 [lat] => 34.779807706414 ==>address
function getLocationByBaiDu($lng, $lat, $ak = '87c3e9435d10e6ad8a06622506836849') {
    $address = "";
    if ($lng < 100) return "";
//    $url = "http://api.map.baidu.com/geocoder/v2/?ak=$ak&output=json&location=$lat,$lng";
    $url = "http://api.map.baidu.com/geocoder/v2/";
    $response = make_post($url, ['output' => 'json', 'ak' => $ak, 'location' => "$lat,$lng"]);
//    $response = get_url_content($url);
    $response = json_decode($response);
    if (!$response) return $address;
    if ($response->status == 0) {
        $province = $response->result->addressComponent->province;
        $city = $response->result->addressComponent->city;
        $district = $response->result->addressComponent->district;
        $sematic_description = $response->result->sematic_description;
        if ($sematic_description)
            $address = $province . $city . $district . $sematic_description;
        else
            $address = $response->result->formatted_address;
    }
    return $address;
}

function getLatLongByGaoDe($address, $key = '5b13176feca1db50c7d32bc662256fac') {

    $location = null;
    if (!$address) return $location;
//    $url = "http://restapi.amap.com/v3/geocode/geo?key=".$key."&address=".$address;
    $url = "http://restapi.amap.com/v3/geocode/geo";
//    die($url);
    $response = make_post($url, ['key' => '5b13176feca1db50c7d32bc662256fac', 'address' => $address]);

    $response = json_decode($response);
    if (!$response) return $location;
    // echo '<pre>';print_r($address); print_r($response);exit;
    if ($response->status == 1 && !empty($response->geocodes[0]->location)) {
        $loc = explode(',', $response->geocodes[0]->location);
        $location['lng'] = empty($loc[0]) ? null : $loc[0];
        $location['lat'] = empty($loc[1]) ? null : $loc[1];
    }

    return $location;
}

//[lng] => 113.68827141439 [lat] => 34.779807706414 ==>address
function getLocationByGaoDe($lng, $lat, $key = '5b13176feca1db50c7d32bc662256fac') {
    $address = "";
    if ($lng < 100) return "";
//    $url = "http://restapi.amap.com/v3/geocode/geo?key=5b13176feca1db50c7d32bc662256fac&address=";
    $url = "http://restapi.amap.com/v3/geocode/regeo?key=" . $key . "&location=" . $lng . "," . $lat;
    $response = get_url_content($url);
    $response = json_decode($response);
    if (!$response) return $address;
    if ($response->status == 0) {
        $province = $response->result->addressComponent->province;
        $city = $response->result->addressComponent->city;
        $district = $response->result->addressComponent->district;
        $sematic_description = $response->result->sematic_description;
        if ($sematic_description)
            $address = $province . $city . $district . $sematic_description;
        else
            $address = $response->result->formatted_address;
    }
    return $address;
}


/*** 根据两点间经纬度坐标（double值），计算两点间距离，单位为米 */
function getDistance($lng1, $lat1, $lng2, $lat2) {
    $earthRadius = 6367000;
    if (strstr($lng1, '.') == 0) {
        $lng1 = substr($lng1, 0, 3) . "." . substr($lng1, 3, 6);
        $lat1 = substr($lat1, 0, 2) . "." . substr($lat1, 2, 6);
        $lng2 = substr($lng2, 0, 3) . "." . substr($lng2, 3, 6);
        $lat2 = substr($lat2, 0, 2) . "." . substr($lat2, 2, 6);
    }
    $lat1 = ($lat1 * pi()) / 180;
    $lng1 = ($lng1 * pi()) / 180;

    $lat2 = ($lat2 * pi()) / 180;
    $lng2 = ($lng2 * pi()) / 180;

    $calcLong = $lng2 - $lng1;
    $calcLat = $lat2 - $lat1;
    $stepOne = pow(sin($calcLat / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLong / 2), 2);
    $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
    $Distance = $earthRadius * $stepTwo;
    return round($Distance) / 1000;
}


/**
 * @param lat 纬度 lon 经度 raidus 单位米
 * return minLat,minLng,maxLat,maxLng
 */
function getAround($lon, $lat, $raidus) {
    $PI = 3.14159265;
    $itude = array();
    $degree = (24901 * 1609) / 360.0;
    $raidusMile = $raidus * 1000;
    // if( strstr($lon,'.') )
    // {
    //     $lon=$lon*1000000;
    //     $lat=$lat*1000000;
    // }

    $dpmLat = 1 / $degree;
    $radiusLat = $dpmLat * $raidusMile;
    $itude['lat'][] = ($lat - $radiusLat);
    $itude['lat'][] = ($lat + $radiusLat);

    $mpdLng = $degree * cos($lat * ($PI / 180));
    $dpmLng = 1 / $mpdLng;
    $radiusLng = $dpmLng * $raidusMile;
    $itude['lng'][] = ($lon - $radiusLng);
    $itude['lng'][] = ($lon + $radiusLng);
    return $itude;
}
