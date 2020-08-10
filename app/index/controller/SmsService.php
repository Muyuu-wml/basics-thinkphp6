<?php

/**
 * 短信相关控制器
 */

namespace app\index\controller;

use app\BaseController; 

class SmsService extends BaseController
{
    public function send($type = 'Ali')
    {
        $validate = \think\facade\Validate::rule('mobile', 'require|mobile');

        $mobile = input('mobile');
        $sms_code_key = '';
        $sms_data = [
            'sms_code' => mt_rand(100000,999999),
            'expire_time' => time()+600,
        ];
        if ($validate->check($mobile)) {
            error($validate->getError());
        }

        $class = "\app\sms\\".$type."SmsCode";
        $res = $class::sendSmsCode($mobile, $sms_data['sms_code']);

        if (!$res) {
            error($res);
        }

        success('发送成功', $sms_data);
    }
}
