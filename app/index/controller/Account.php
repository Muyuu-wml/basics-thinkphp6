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
            'mobile'       => input('mobile'),
            'password'     => trim(input('password')),
            'type'         => input('type'),
            'sms_code'     => input('sms_code'),
            'sms_code_key' => input('sms_code_key'),
        ];

        if (!$login_data['type']) {
            try {
                validate(Login::class)->check($login_data);
            } catch (ValidateException $e) {
                return error($e->getError());
            }
            $res = User::login($login_data);
        } else {
            // 验证码登录
            if (empty($login_data['sms_code']) || empty($login_data['sms_code_key'])) {
                error('验证码不能为空');
            }
            // 判断短信验证码是否正确
            checkSmsCode($login_data['sms_code_key'], $login_data['sms_code']);
            $res = User::getUserByMobile($login_data['mobile']);
        }

        if($res['status'] == 1){
            error('此用户已被锁定');
        }

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
            'password'     => trim(input('password')),
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
        checkSmsCode($register_data['sms_code_key'], $register_data['sms_code']);

        $res = User::register($register_data);
        if ($res == true) {
            success('注册成功');
        } else {
            error('注册失败');
        }
    }

    /**
     * 忘记密码
     *
     * @return void
     */
    public function forgetPassword()
    {
        $forget_password_data = [
            'mobile'       => input('mobile'),
            'password'     => trim(input('password')),
            'sms_code'     => input('sms_code'),
            'sms_code_key' => input('sms_code_key'),
        ];

        try {
            validate(Login::class)->check($forget_password_data);
        } catch (ValidateException $e) {
            error($e->getError());
        }

        if (empty($forget_password_data['sms_code']) || empty($forget_password_data['sms_code_key'])) {
            error('验证码不能为空');
        }

        // 判断短信验证码是否正确
        checkSmsCode($forget_password_data['sms_code_key'], $forget_password_data['sms_code']);
    }
}
