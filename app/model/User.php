<?php

namespace app\model;

use think\Model;
use app\model\InviteRecord;

class User extends Model
{
    protected $autoWriteTimestamp = true;

    /**
     * 注册
     *
     * @param [type] $register_date 注册数据
     * @return void
     */
    public static function register($register_date)
    {
        if (self::where('mobile', $register_date['mobile'])->find()) {
            return ['status' => false, 'smg' => '手机号已注册'];
        }

        $salt = salt();
        self::startTrans();
        try {
            $user =  User::create([
                'username' => '用户：' . $register_date['mobile'],
                'mobile'   => $register_date['mobile'],
                'password' => encryption($register_date['password'], $salt),
                'salt'     => $salt,
            ]);

            // 判断是否是邀请用户
            if ($register_date['invite_code']) {
                $invite_user = self::where('invite_code', $register_date['invite_code'])->find();
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
            return ['status' => false, 'smg' => '数据库内部错误'];
        }

        return true;
    }
}
