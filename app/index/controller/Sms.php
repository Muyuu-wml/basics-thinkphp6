<?php

/**
 * 短信相关控制器
 */
namespace app\index\controller;

use app\BaseController;
use app\common\sms\SmsService;

class Sms extends BaseController
{
    private $mobile;

    public function initialize()
    {
        $mobile = input('mobile');

        $validate = \think\facade\Validate::rule(['mobile' => ['require', 'mobile']]);
        if ($validate->check(['mobile' => $mobile])) {
            $this->mobile = $mobile;
        } else {
            error($validate->getError());
        }
    }

    /**
     * 注册
     *
     * @return void
     */
    public function registerSms()
    {
        $sms_data = SmsService::send($this->mobile, 'register');
        if($sms_data){
            success('发送成功', $sms_data);
        } else {
            error('发送失败');
        }
    }

    /**
     * 登录
     *
     * @return void
     */
    public function loginSms()
    {
        $sms_data = SmsService::send($this->mobile, 'login');
        if($sms_data){
            success('发送成功', $sms_data);
        } else {
            error('发送失败');
        }
    }

    /**
     * 忘记密码
     *
     * @return void
     */
    public function forgetPassSms()
    {
        $sms_data = SmsService::send($this->mobile, 'forgetpass');
        if($sms_data){
            success('发送成功', $sms_data);
        } else {
            error('发送失败');
        }
    }
}