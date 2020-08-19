<?php

namespace app\model;

use think\Model;
use app\model\InviteRecord;
use think\facade\Env;

class User extends Model
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
        $user = self::where('mobile', $login_data['mobile'])->where('delete_time', null)->find();
        if ($user) {
            if ($user['password'] == encryption($login_data['password'], $user['salt'])) {
                try {
                    $user->last_login_time = date('Y-m-d H:i:s');
                    $user->save();
                } catch (\Exception $e) {
                    error('数据库内部错误');
                }
                return $user;
            } else {
                error('密码错误');
            }
        } else {
            error('此手机号未注册');
        }
    }

    /**
     * 注册
     *
     * @param [type] $register_date 注册数据
     * @return void
     */
    public static function register($register_data)
    {
        if (self::where('mobile', $register_data['mobile'])->where('delete_time', null)->find()) {
            error('手机号已注册');
        }

        $salt = salt();
        self::startTrans();
        try {
            $user =  self::create([
                'username' => '用户：' . $register_data['mobile'],
                'mobile'   => $register_data['mobile'],
                'password' => encryption($register_data['password'], $salt),
                'salt'     => $salt,
            ]);

            // 判断是否是邀请用户
            if ($register_data['invite_code']) {
                $invite_user = self::where('invite_code', $register_data['invite_code'])->where('delete_time', null)->find();
                if ($invite_user) {
                    InviteRecord::create([
                        'user_id'     => $invite_user['id'],
                        'to_user_id'  => $user->id,
                        'create_time' => date('Y-m-d H:i:s')
                    ]);
                }
            }
            self::commit();
        } catch (\Exception $e) {
            self::rollback();
            error('数据库内部错误');
        }

        return true;
    }

    /**
     * 忘记密码
     *
     * @param [type] $forget_password_data 忘记密码数据
     * @return void
     */
    public static function forgetPassword($forget_password_data, $user_id = 0)
    {
        if ($user_id) {
            $where = [
                ['id', '=', $user_id]
            ];
        } else {
            $where = [
                ['mobile', '=', $forget_password_data['mobile']]
            ];
        }

        try {
            $salt = self::where($where)->value('salt');
            self::where($where)->update(['password' => encryption($forget_password_data['password'], $salt)]);
        } catch (\Exception $e) {
            error('数据库内部错误');
        }

        return true;
    }

    /**
     * 通过用户手机号获取用户信息
     *
     * @param [type] $mobile 手机号
     * @return void
     */
    public static function getUserByMobile($mobile)
    {
        $user = self::field('id, username, nickname, mobile, email, balance, attr, personal_profile, invite_code, status')->where('mobile', $mobile)->where('delete_time', null)->find();
        if ($user) {
            return $user;
        } else {
            error('没有此用户信息');
        }
    }

    /**
     * 通过用户id获取用户信息
     *
     * @param [type] $user_id 用户id
     * @return void
     */
    public static function getUserInfoById($user_id)
    {
        $user = self::field('id, username, nickname, mobile, email, balance, attr, personal_profile, invite_code, status')->where('id', $user_id)->where('delete_time', null)->find();
        if ($user) {
            if (empty($user['invite_code'])) {
                $invite_code = self::getInviteCode();
                try {
                    self::where('id', $user_id)->update(['invite_code' => $invite_code]);
                    $user['invite_code'] = $invite_code;
                } catch (\Exception $e) {
                    error('数据库内部错误');
                }
            }
            return $user;
        } else {
            error('没有此用户信息');
        }
    }

    /**
     * 递归判断邀请码是否重复
     *
     * @return void
     */
    public static function getInviteCode()
    {
        $invite_code = inviteCode();
        if (self::recode($invite_code)) {
            return $invite_code;
        } else {
            self::getInviteCode();
        }
    }

    public static function recode($invite_code)
    {
        if (self::where('invite_code', $invite_code)->find()) {
            return false;
        }
        return true;
    }

    /**
     * 修改用户信息
     *
     * @param [type] $user_id 用户id
     * @param [type] $update_user_info_data 用户修改信息
     * @return void
     */
    public static function updateUserInfo($user_id, $update_user_info_data)
    {
        try {
            self::where('id', $user_id)->update($update_user_info_data);
            return true;
        } catch (\Exception $e) {
            error('数据库内部错误');
        }
    }

    /**
     * 用户修改密码
     *
     * @param [type] $user_id 用户id
     * @param [type] $update_password_date 用户修改密码数据
     * @return void
     */
    public static function updateUserPassword($user_id, array $update_password_data)
    {
        $password = self::where('id', $user_id)->value('password');
        $salt = self::where('id', $user_id)->value('salt');
        if (encryption($update_password_data['old_password'], $salt) !== $password) {
            error('原密码错误');
        }
        if (encryption($update_password_data['new_password'], $salt) == $password) {
            error('新密码不能和原密码相同');
        }

        try {
            self::where('id', $user_id)->update(['password' => encryption($update_password_data['new_password'], $salt)]);
            return true;
        } catch (\Exception $e) {
            error('数据库内部错误');
        }
    }

    /**
     * 头像获取器
     *
     * @param [type] $value
     * @param [type] $data
     * @return void
     */
    public function getAttrAttr($value,$data)
    {
        if (empty($value)) {
            return null;
        }
        if (Env::get('FILE.ISLOCAL')) {
            return Env::get('FILE.PATH');
        }
    }
}
