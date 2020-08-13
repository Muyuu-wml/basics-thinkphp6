<?php
/**
 * 账户相关控制器
 */
namespace app\index\controller;

use app\BaseController;
use app\index\validate\Login;
use app\common\controller\TokenService;
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
        $res = '';
        if (!$login_data['type']) {
            try {
                validate(Login::class)->scene('login_password')->check($login_data);
            } catch (ValidateException $e) {
                return error($e->getError());
            }
            $res = User::login($login_data);
        } else {
            try {
                validate(Login::class)->scene('sms')->check($login_data);
            } catch (ValidateException $e) {
                return error($e->getError());
            }
            // 判断短信验证码是否正确
            checkSmsCode($login_data['sms_code_key'], $login_data['sms_code']);
            $res = User::getUserByMobile($login_data['mobile']);
        }

        if($res['status'] == 1){
            error('此用户已被锁定');
        }
        $jwt_data = TokenService::getToken($res['id']);
        success('登录成功', $jwt_data);
    }

    /**
     * 通过RefreshToken获取AccessToken
     *
     * @return void
     */
    public function getAccessTokenByRefreshToken()
    {   
        $access_token = request()->header('Authorization');
        $refresh_token = input('refresh_token');
        
        if (empty($access_token) || empty($refresh_token)) {
            error('缺少token');
        }

        $jwt_data = TokenService::getAccessTokenByRefreshToken($access_token, $refresh_token);
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
        checkSmsCode($register_data['sms_code_key'], $register_data['sms_code']);

        $res = User::register($register_data);
        if ($res == true) {
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
            validate(Login::class)->scene('sms')->check($forget_password_data);
        } catch (ValidateException $e) {
            error($e->getError());
        }

        // 判断短信验证码是否正确
        checkSmsCode($forget_password_data['sms_code_key'], $forget_password_data['sms_code']);

        $res = User::forgetPassword($forget_password_data);
        if ($res == true) {
            success('修改成功');
        } else {
            error('修改失败');
        }
    }
}
