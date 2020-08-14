<?php
namespace app\common\sms;

interface BaseSmsCode
{
    public static function sendSmsCode($mobile, $sms_code);
    public static function getErrorMessage($code);
}