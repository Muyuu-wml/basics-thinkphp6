<?php

namespace app\api\validate;

use think\Validate;

class AdminLogin extends Validate
{
    protected $rule = [
        'username' => ['require'],
        'password' => ['require'],
    ];

    protected $message  =   [
        'username.require' => '用户名不能位空',
        'password.require' => '密码不能为空',
    ];
}
