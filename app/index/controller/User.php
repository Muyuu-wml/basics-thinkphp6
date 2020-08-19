<?php
/**
 * 用户相关控制器
 */
namespace app\index\controller;

use app\BaseController;
use app\model\InviteRecord;
use app\model\User as UserModel;

class User extends Auth
{
    /**
     * 用户信息
     *
     * @return void
     */
    public function getUserInfo()
    {
        $where = [
            ['id', '=', input('user_id', $this->getUserId())],
            ['status', '=', 0],
            ['delete_time', '=', null]
        ];
        $user_info = UserModel::getUserInfo($where);
        success('用户信息', $user_info);
        
    }

    /**
     * 修改用户信息
     *
     * @return void
     */
    public function updateUserInfo()
    {
        $update_user_info_data = [
            'username'         => input('username', ''),
            'nickname'         => input('nickname', ''),
            'email'            => input('email', ''),
            'real_name'        => input('real_name', ''),
            'attr'             => input('attr', ''),
            'personal_profile' => input('personal_profile', '')
        ];
        $update_user_info_data = array_filter($update_user_info_data);

        $res = UserModel::updateUserInfo($this->getUserId(), $update_user_info_data);
        if ($res) {
            success('修改成功');
        } else {
            error('修改失败');
        }
    }

    /**
     * 修改密码
     *
     * @return void
     */
    public function updateUserPassword()
    {
        $update_password_data = [
            'new_password' => input('new_password'),
            'old_password' => input('old_password')
        ];

        $res = UserModel::updateUserPassword($this->getUserId(), $update_password_data);
        if ($res) {
            success('修改成功');
        } else {
            error('修改失败');
        }
    }

    /**
     * 邀请记录
     *
     * @return void
     */
    public function inviteRecord()
    {
        $where = [
            ['id', '=', $this->getUserId()],
        ];

        $invite_list = InviteRecord::getInviteRecord($where);
        success('用户邀请列表', $invite_list);
    }
}