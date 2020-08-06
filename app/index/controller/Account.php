<?php
namespace app\index\controller;

use app\BaseController;
use app\index\validate\Login;
use think\exception\ValidateException;
use app\index\model\UserModel;
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
        dd(mb_strpos('http://petmore.dev.mengjing.site/storage/images/5eea1fe7c3952a4fea7dfb126f3276d9.jpg', 'http://'));
        $login_data = [
            'mobile' => strtolower(input('mobile')),
            'password' => input('password')
        ];

        try {
            validate(Login::class)->check($login_data);
        } catch (ValidateException $e) {
            return error($e->getError());
        }

        
    }
}