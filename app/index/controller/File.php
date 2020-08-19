<?php

/**
 * 文件相关控制器
 */

namespace app\index\controller;

use app\common\file\FileService;
use app\common\file\QiniuService;

class File extends Auth
{
    /**
     * 单文件上传
     *
     * 默认上传文件名为file，大小为4mb，文件类型为jpg,png,gif,jpeg，可自行更改，请使用post请求
     * @return void
     */
    public function upload()
    {
        // 文件上传请使用POST请求
        if (!request()->isPost()){
            error('请求类型错误');
        }

        $file = request()->file('file');
        if (!empty($file)) {
            $res = FileService::uploadFile($file);
            if ($res) {
                success('上传成功', $res);
            } else {
                error('上传失败');
            }
        } else {
            error('没有上传任何文件');
        }
    }

    /**
     * 获取七牛上传token
     *
     * @return void
     */
    public function qiniuToken()
    {
        $token = QiniuService::getToken();
        success('七牛token', $token);
    }
}
