<?php

/**
 * 账户相关控制器
 */

namespace app\api\controller;

use app\BaseController;
use app\api\validate\Login;
use app\common\token\TokenService;
use app\common\sms\SmsService;
use think\exception\ValidateException;
use app\model\User;

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
        // type false的时候为密码登录，true的时候为短信登录
        if (!$login_data['type']) {
            try {
                validate(Login::class)->scene('login_password')->check($login_data);
            } catch (ValidateException $e) {
                error($e->getError());
            }
            $res = User::login($login_data);
        } else {
            try {
                validate(Login::class)->scene('sms')->check($login_data);
            } catch (ValidateException $e) {
                error($e->getError());
            }
            // 判断短信验证码是否正确
            SmsService::checkSmsCode($login_data['sms_code_key'], $login_data['sms_code']);
            $res = User::getUserInfo(['moblie' => $login_data['mobile']]);
        }

        if ($res['status'] == 1) {
            error('此用户已被锁定');
        }

        $jwt_data = TokenService::getToken($res['id']);
        success('登录成功', $jwt_data);
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
            validate(Login::class)->scene('register')->check($register_data);
        } catch (ValidateException $e) {
            error($e->getError());
        }

        // 判断短信验证码是否正确
        SmsService::checkSmsCode($register_data['sms_code_key'], $register_data['sms_code']);

        $res = User::register($register_data);
        if ($res === true) {
            success('注册成功');
        } else {
            error('注册失败');
        }
    }

    /**
     * 忘记密码,通过短信修改
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
            validate(Login::class)->scene('register')->check($forget_password_data);
        } catch (ValidateException $e) {
            error($e->getError());
        }

        // 判断短信验证码是否正确
        SmsService::checkSmsCode($forget_password_data['sms_code_key'], $forget_password_data['sms_code']);

        $res = User::forgetPassword($forget_password_data);
        if ($res === true) {
            success('修改成功');
        } else {
            error('修改失败');
        }
    }
}
