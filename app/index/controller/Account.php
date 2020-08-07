<?php

namespace app\index\controller;

use app\BaseController;
use app\index\validate\Login;
use think\exception\ValidateException;
use app\model\User;
use Firebase\JWT\JWT;

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

        $res = User::login($login_data);

        $token_arr = [
            'user_id'     => $res['id'],
            'expire_time' => strtotime('+10 day') // token的过期时间为十天
        ];
        $key = config('system.jwt_key');
        $jwt_token = JWT::encode($token_arr, $key);
        $data['token'] = $jwt_token;
        success('登录成功', $data);
    }

    /**
     * 注册
     *
     * @return void
     */
    public function register()
    {
        $register_data = [
            'mobile'       => input('mobile'),
            'password'     => trim(strtolower(input('password'))),
            'sms_code'     => input('sms_code'),
            'sms_code_key' => input('sms_code_key'),
            'invite_code'  => input('invite_code')
        ];

        try {
            validate(Login::class)->check($register_data);
        } catch (ValidateException $e) {
            error($e->getError());
        }

        if (empty($register_data['sms_code']) || empty($register_data['sms_code_key'])) {
            error('验证码不能为空');
        }

        // 判断短信验证码是否正确
        $cache_sms_code_data = cache($register_data['sms_code_key']);
        if ($cache_sms_code_data === false) {
            error('验证码错误');
        } else {
            // 短信验证码是否过期
            if (time() > $cache_sms_code_data['expire_time']) {
                error('验证码已过期');
            } else {
                // 判断输入的验证码是否正确
                if ($register_data['sms_code'] != $cache_sms_code_data['sms_code']) {
                    error('验证码错误');
                }
            }
        }

        $res = User::register($register_data);
        if ($res == true) {
            success('注册成功');
        } else {
            error('注册失败');
        }
    }
}
