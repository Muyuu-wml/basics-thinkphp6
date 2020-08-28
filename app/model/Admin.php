<?php

namespace app\model;

use think\Model;
use think\facade\Env;

class Admin extends Model
{
    protected $autoWriteTimestamp = true;

    /**
     * 登录
     *
     * @param [type] $login_data
     * @return void
     */
    public static function login($login_data)
    {
        $admin = self::where('username', $login_data['username'])->where('delete_time', null)->find();
        if ($admin) {
            if ($admin['password'] == encryption($login_data['password'], $admin['salt'])) {
                try {
                    $admin->last_login_time = date('Y-m-d H:i:s');
                    $admin->save();
                } catch (\Exception $e) {
                    error('数据库内部错误');
                }
                return $admin;
            } else {
                error('密码错误');
            }
        } else {
            error('没有此用户');
        }
    }

    /**
     * 添加管理员账号
     *
     * @param [type] $admin_data
     * @return void
     */
    public static function createAdminAccount(array $admin_data)
    {
        if (self::where('username', $admin_data['username'])->where('delete_time', null)->find()) {
            error('用户名重复');
        }

        $admin_data['salt'] = salt();
        $admin_data['password'] = encryption($admin_data['password'], $admin_data['salt']);
        
        try {
            self::create($admin_data);
            return true;
        } catch (\Exception $e) {
            error($e->getMessage());
        }
    }

    /**
     * 管理员列表
     *
     * @param [type] $where
     * @return void
     */
    public static function getList($where)
    {
        $list = self::where($where)->select();
        if (empty($list)) {
            $list = [];
        }
        return $list;
    }

}