<?php
/**
 * 用户相关控制器
 */
namespace app\index\controller;

use app\BaseController;
use app\model\User as UserModel;

class User extends BaseController
{
    /**
     * 用户信息
     *
     * @return void
     */
    public function getUserInfo()
    {
        $user_info = UserModel::getUserInfoById(1);
        success('用户信息', $user_info);
    }

    /**
     * 修改用户信息
     *
     * @return void
     */
    public function updateUserInfo()
    {
        
    }
}