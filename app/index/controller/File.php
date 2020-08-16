<?php

/**
 * 文件相关控制器
 */

namespace app\index\controller;

use app\common\file\FileService;

class File extends Auth
{

    /**
     * 文件上传请使用POST请求
     */
    public function initialize()
    {
        if (request()->isGet()){
            error('请求类型错误');
        }
    }

    /**
     * 单文件上传
     *
     * 默认上传文件名为file，大小为4mb，文件类型为jpg,png,gif,jpeg，可自行更改
     * @return void
     */
    public function upload()
    {
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
}
