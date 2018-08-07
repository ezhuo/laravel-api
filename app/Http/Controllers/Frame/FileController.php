<?php

namespace App\Http\Controllers\Frame;

use Illuminate\Http\Request;
use Storage;
use Illuminate\Http\Response;
use App\Models\Frame\Base;

class FileController extends AppDataController {

    public function __construct(Request $request, Base $model) {
        parent::__construct($request, $model);
        $this->middleware('auth', ['except' => ['download', 'upload_ckeditor']]);
    }

    /**
     * 文件上传
     * @param Request $request
     * @return Response
     */
    public function upload(Request $request) {
        $result = [];
        $return = [];
        $http_code = HTTP_WRONG;
        if ($request->isMethod('post')) {
            $return = $this->UploadFile($request, 'file');
        }
        if (!empty($return)) {
            $result = $return;
            $result['uid'] = $return['fileName'];
            $http_code = HTTP_OK;
        } else {
            $result['response'] = 'error';
        }
        return response($result, $http_code);
    }

    public function upload_ckeditor(Request $request) {
        $result = [
            "uploaded" => 0,
            "error" => [
                "message" => ""
            ]
        ];
        if ($request->hasFile('upload')) {//upload为ckeditor默认的file提交ID
            $return = $this->UploadFile($request, 'upload');
        }
        if (!empty($return)) {
            $result = [
                "uploaded" => 1,
                "fileName" => $return['fileName'],
                "url" => '/file/show/' . $return['url'],
            ];
        } else {
            $result['error']['message'] = '上传失败！';
        }
        return response($result, HTTP_OK);
    }

    /**
     * 下载文件
     * @param Request $request
     * @return Response
     */
    public function download(Request $request) {
//        dump($_REQUEST);
        $f = $request["f"];
        $n = $request['n'];
//        dd($n);
//        die();
        if (empty($f)) {
            return response("抱歉，下载非法！");
        } else {
            $f = public_path(APP_UPLOAD_DRIVER) . '/' . $f;
//            $f = base_path(APP_UPLOAD_DRIVER) . '/' . $f;
//            $f = app_path(APP_UPLOAD_DRIVER) . '/' . $f;

            if (isImage($f)) {
                Header('Location: /' . APP_UPLOAD_DRIVER . '/' . $_REQUEST["f"]);
                exit;
            }
            if (file_exists($f)) {
                $n = urldecode($n);
//                $n = get_filename_bybrowser(empty($n) ? basename($f) : $n);
                return response()->download($f, $n);
            } else {
                return response("抱歉，文件不存在！");
            }
        }
    }

}
