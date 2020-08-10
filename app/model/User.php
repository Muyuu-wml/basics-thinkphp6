<?php

namespace app\model;

use think\Model;
use app\model\InviteRecord;

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
     * @param [type] $mobile
     * @return void
     */
    public static function getUserByMobile($mobile)
    {
        if (empty($mobile)) {
            error('手机号不能为空');
        }

        $user = self::where('mobile', $mobile)->where('delete_time', null)->find();
        if ($user) {
            return $user;
        } else {
            error('没有此用户信息');
        }
    }
}
