<?php

namespace App\Http\Controllers\Frame;

use Illuminate\Http\Request;
use Storage;
use Illuminate\Http\Response;
use App\Models\Frame\Base;

class FileController extends AppDataController {

    public function __construct(Request $request, Base $model) {
        parent::__construct($request, $model);
        $this->middleware('auth', ['except' => ['download']]);
    }

    /**
     * 文件上传
     * @param Request $request
     * @return Response
     */
    public function upload(Request $request) {
        $result = ['status' => 0];
        if ($request->isMethod('post')) {
            $file = $request->file('file');
            // 文件是否上传成功
            if ($file->isValid()) {
                // 获取文件相关信息
                $originalName = $file->getClientOriginalName(); // 文件原名
                $ext = $file->getClientOriginalExtension();     // 扩展名
                $realPath = $file->getRealPath();   //临时文件的绝对路径
                $size = $file->getClientSize();

                $type = $file->getClientMimeType();     // image/jpeg
                $arr = explode('/', $type);
                $ext = $arr[sizeof($arr) - 1];
                if (strlen($ext) > 4) {
                    $arr = explode('.', $originalName);
                    $ext = $arr[sizeof($arr) - 1];
                }

                // 上传文件
                $realname = time() . '_' . mt_rand() . "." . $ext;
                $filename = date('Y-m-d') . "/" . $realname;
//                $filename = date('Y-m-d') . "/" . uniqid() . "." . $ext;

                // 使用我们新建的uploads本地存储空间（目录）
                $bool = Storage::disk(APP_UPLOAD_DRIVER)->put($filename, file_get_contents($realPath));
                if ($bool) {
                    $result = [
                        'path' => $filename,
                        'name' => $realname,
                        'type' => $type,
                        'size' => $size,
                        'status' => 1,
                        'dt' => get_dt()
                    ];
                }
            }
        }
        return response($result);
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
