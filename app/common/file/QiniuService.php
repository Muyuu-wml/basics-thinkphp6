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
}