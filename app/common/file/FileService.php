<?php

/**
 * 文件相关控制器
 */

namespace app\common\file;

use app\BaseController;
use think\facade\Env;
use think\exception\ValidateException;

class FileService extends BaseController
{
    public static function uploadFile($file, $file_size = 4194304, $file_ext = 'jpg,png,gif,jpeg')
    {
        try {
            validate(['file' => ['fileSize' => $file_size, 'fileExt' => $file_ext]])->check(['file' => $file]);
            $savename = \think\facade\Filesystem::disk('public')->putFile('topic', $file);
            $data = [
                'file_path'          => $savename, // 相对路径， 数据库保存用
                'complete_file_path' => Env::get('file.path') . $savename, // 完整路径， 前端显示用
            ];
            return $data;
        } catch (ValidateException $e) {
            error($e->getMessage());
        }
    }
}
