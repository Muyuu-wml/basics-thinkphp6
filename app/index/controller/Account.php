<?php

namespace app\index\controller;

use app\BaseController;
use app\index\validate\Login;
use think\exception\ValidateException;
use app\model\User;
use Firebase\JWT\JWT;
use think\facade\Db;

class Account extends BaseController
{
    /**
     * 登录
     *
     * @return void
     */
    public function login()
    {
        $login_data = [
            'mobile'   => input('mobile'),
            'password' => trim(strtolower(input('password')))
        ];

        try {
            validate(Login::class)->check($login_data);
        } catch (ValidateException $e) {
            return error($e->getError());
        }
    }

    /**
     * 注册
     *
     * @return void
     */
    public function register()
    {
        $register_date = [
            'mobile'       => input('mobile'),
            'password'     => trim(strtolower(input('password'))),
            'sms_code'     => input('sms_code'),
            'sms_code_key' => input('sms_code_key'),
            'invite_code'  => input('invite_code')
        ];

        try {
            validate(Login::class)->check($register_date);
        } catch (ValidateException $e) {
            return error($e->getError());
        }

        if (empty($register_date['sms_code']) || empty($register_date['sms_code_key'])) {
            return error('验证码不能为空');
        }

        判断短信验证码是否正确
        $cache_sms_code_data = cache($register_date['sms_code_key']);
        if ($cache_sms_code_data === false) {
            return error('验证码错误');
        } else {
            // 短信验证码是否过期
            if (time() > $cache_sms_code_data['expire_time']) {
                return error('验证码已过期');
            } else {
                // 判断输入的验证码是否正确
                if ($register_date['sms_code'] != $cache_sms_code_data['sms_code']) {
                    return error('验证码错误');
                }
            }
        }

        $res = User::register($register_date);
        if ($res['status'] == false) {
            return error($res['smg']);
        } else {
            return success('注册成功');
        }
    }
}
