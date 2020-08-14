<?php
/**
 * 短信宝短信服务
 */
namespace app\common\sms;
use think\facade\Env;

class DxbSmsCode implements BaseSmsCode
{
    public static function sendSmsCode($mobile, $sms_code)
    {
        $content = '【'.Env::get('APP.APP_NAME').'】您的短信验证码为：' . $sms_code . '。';
        $username = config('system.dxd_username');
        $password = md5(config('system.dxb_password'));
        $url = "http://api.smsbao.com/sms?u={$username}&p={$password}&m={$mobile}&c={$content}";
        $res = http_get($url);
        if ($res == '0') {
            return true;
        } else {
            self::getErrorMessage($res);
        }
    }

    public static function getErrorMessage($code)
    {
        $message = [
            '30' => '错误密码',
            '40' => '账号不存在',
            '41' => '余额不足',
            '43' => 'IP地址限制',
            '50' => '内容含有敏感词汇',
            '51' => '手机号码不正确'
        ];
        if (isset($message[$code])) {
            return $message[$code];
        }
        return '未知原因';
    }
}