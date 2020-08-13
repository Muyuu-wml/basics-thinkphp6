<?php

/**
 * 文件相关控制器
 */

namespace app\common\controller;

use app\BaseController;
use think\facade\Env;
use think\exception\ValidateException;

class FileService extends BaseController
{
    /**
     * 单文件上传
     *
     * @return void
     */
    public function uploadFile()
    {
        $files = request()->file('file');
        try {
            if (!empty($file)) {
                validate(['file' => ['fileSize' => 10240, 'fileExt' => 'jpg,png,gif,jpeg']])->check($files);
                $savename = \think\facade\Filesystem::disk('public')->putFile('topic', $file);
                $data = [
                    'file_path'          => $savename, // 相对路径， 数据库保存用
                    'complete_file_path' => Env::get('file.path') . $savename, // 完整路径， 前端显示用
                ];
                success('上传成功', $data);
            } else {
                error('没有上传任何文件');
            }
        } catch (ValidateException $e) {
            error($e->getMessage());
        }
    }
}
