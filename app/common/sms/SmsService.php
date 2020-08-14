<?php

/**
 * 短信相关控制器
 */

namespace app\common\sms;

use app\BaseController;
use think\facade\Cache;

class SmsService extends BaseController
{
    public static function send($mobile, $action, $type = 'Ali')
    {
        if(self::isSend($mobile)){
            error('请勿频繁发送');
        }

        $sms_data = [
            'sms_code_key' => $mobile . ':' . $action . ':sms_code_key',
            'sms_code' => mt_rand(100000, 999999),
        ];

        $class = "\app\common\sms\\" . $type . "SmsCode";
        $res = $class::sendSmsCode($mobile, $sms_data['sms_code']);
        
        if ($res !== true) {
            error($res);
        } else {
            Cache::set($sms_data['sms_code_key'], $sms_data['sms_code'], 60*5);
            self::setSendTime($mobile);
        }

        return $sms_data;
    }

    /**
     * 设置可再次发送时间
     *
     * @param [type] $mobile
     * @return void
     */
    public static function setSendTime($mobile, $expire = 60)
    {
        $key = 'sms_coed:'.$mobile;
        Cache::set($key, 1, $expire);
    }

    /**
     * 判断是否可以再次发送短信
     *
     * @param [type] $mobile
     * @return boolean
     */
    public static function isSend($mobile): bool
    {
        $key = 'sms_coed:'.$mobile;
        if (Cache::get($key)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判断短信验证码是否正确
     *
     * @param string $sms_code_key
     * @param integer $sms_code
     * @return void
     */
    public static function checkSmsCode($sms_code_key, $sms_code)
    {
        $cache_sms_code = Cache::get($sms_code_key);
        if ($cache_sms_code === false) {
            error('验证码错误');
        } else {
            // 判断输入的验证码是否正确
            if ($sms_code != $cache_sms_code) {
                error('验证码错误');
            }
        }
    } 
}
