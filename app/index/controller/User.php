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
        // $user_info = UserModel::getUserInfoById($this->getUserId());
        // success('用户信息', $user_info);
    }

    /**
     * 修改用户信息
     *
     * @return void
     */
    public function updateUserInfo()
    {
        
    }

    /**
     * 修改密码
     *
     * @return void
     */
    public function updateUserPassword()
    {

    }

    /**
     * 获取我的邀请码
     *
     * @return void
     */
    public function inviteCode()
    {   

    }

    /**
     * 邀请记录
     *
     * @return void
     */
    public function inviteRecord()
    {

    }
}