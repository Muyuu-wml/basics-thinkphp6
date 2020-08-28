<?php
namespace app\admin\controller;

use app\index\validate\AdminLogin;
use think\exception\ValidateException;
use app\model\Admin;
use app\common\token\TokenService;

class Login extends Auth
{
    /**
     * 管理员登录
     *
     * @return void
     */
    public function login()
    {
        $admin_login_data = [
            'username' => input('username'),
            'password' => input('password')
        ];

        try {
            validate(AdminLogin::class)->check($admin_login_data);
        } catch (ValidateException $e) {
            error($e->getError());
        }

        $res = Admin::login($admin_login_data);

        $jwt_data = TokenService::getToken($res['id']);
        success('登录成功', $jwt_data);
    }
}