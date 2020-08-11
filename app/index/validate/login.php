<?php

namespace app\index\validate;

use think\Validate;

class Login extends Validate
{
    protected $rule = [
        'mobile'       => ['require', 'mobile'],
        // 正则表达式6-16位字符（英文/数字/符号）三种组合
        'password'     => ['require', 'regex' => '/(?=.*[0-9])(?=.*[A-Za-z]).{6,16}/'],
        // 正则表达式6-16位字符（英文/数字/符号）至少两种或下划线组合
        // 'password'  => ['require', 'regex' => '/^(\w*(?=\w*\d)(?=\w*[A-Za-z])\w*){6,16}$/'],
        'sms_code'     => ['require'],
        'sms_code_key' => ['sms_code_key'],
    ];

    protected $message  =   [
        'mobile.require'       => '手机不能位空',
        'mobile.mobile'        => '手机格式错误',
        'password.require'     => '密码不能为空',
        'password.regex'       => '密码格式错误',
        'sms_code.require'     => '验证码不能位空',
        'sms_code_key.require' => '验证码key不能位空',
    ];

    protected $scene = [
        'sms'            => ['mobile', 'sms_code', 'sms_code_key'],
        'register'       => ['mobile', 'password', 'sms_code', 'sms_code_key'],
        'login_password' => ['mobile', 'password']
    ];
}
