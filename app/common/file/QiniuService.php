<?php

/**
 * 七牛相关控制器
 */

namespace app\common\file;

use app\BaseController;
use Qiniu\Auth;

class QiniuService extends BaseController
{
    /**
     * 获取七牛token
     *
     * @return void
     */
    public static function getToken()
    {
        $qiniu_access_key    = config('system.qiniu_access_key');
        $qiniu_access_secret = config('system.qiniu_access_secret');
        $qiniu_bucket        = config('system.qiniu_bucket');
        $auth = new Auth($qiniu_access_key, $qiniu_access_secret);
        $upToken = $auth->uploadToken($qiniu_bucket);
        return $upToken;
    }

    /**
     * 上传七牛文件
     *
     * @param [type] $file
     * @return void
     */
    public function upload($file)
    {
        // 要上传图片路径
        $filePath = $file->getRealPath();
        $ext = $file->getOriginalExtension();
        // 上传到七牛的名称
        $key = substr(md5($filePath), 0, 5) . date('YmdHis') . rand(1000, 9999) . '.' . $ext;
        // 填写 asscess key 和 secret key
        $qiniu_access_key    = config('system.qiniu_access_key');
        $qiniu_access_secret = config('system.qiniu_access_secret');
        // 鉴权
        $auth = new Auth($qiniu_access_key, $qiniu_access_secret);
        // 要上传的空间
        $qiniu_bucket = config('system.qiniu_bucket');
        $upToken = $auth->uploadToken($qiniu_bucket);
        // 初始化 UploadManager 对象并进行文件的上传
        $uploadMgr = new \Qiniu\Storage\UploadManager();
        // 调用 putFile 方法进行文件上传
        list($ret, $err) = $uploadMgr->putFile($upToken, $key,  $filePath);

        if ($err !== null) {
            return false;
        } else {
            $domain = config('system.qiniu_domain');
            // 返回完整路径
            $path = 'http://' . $domain . '/' . $ret['key'];
            return $path;
        }
    }
}
