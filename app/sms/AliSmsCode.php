<?php

namespace app\sms;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class AliSmsCode
{
    public static function sendSmsCode($mobile, $sms_code)
    {
        $accessKeyId  = config('system.access_key_id');
        $accessSecret = config('system.access_secret'); //注意不要有空格
        $signName     = config('system.sign_name'); //配置签名
        $templateCode = config('system.template_code'); //配置短信模板编号
        //TODO 随机生成一个6位数
        //TODO 短信模板变量替换JSON串,友情提示:如果JSON中需要带换行符,请参照标准的JSON协议。
        $jsonTemplateParam = json_encode(['code' => $sms_code]);

        AlibabaCloud::accessKeyClient($accessKeyId, $accessSecret)
            ->regionId('cn-hangzhou')
            ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->options([
                    'query' => [
                        'RegionId'      => 'cn-hangzhou',
                        'PhoneNumbers'  => $mobile, //目标手机号
                        'SignName'      => $signName,
                        'TemplateCode'  => $templateCode,
                        'TemplateParam' => $jsonTemplateParam,
                    ],
                ])
                ->request();
            $opRes = $result->toArray();
            //print_r($opRes);
            if ($opRes && $opRes['Code'] == "OK") {
                //进行Cookie保存
                return true;
            } else {
                return self::getErrorMessage($opRes['status']);
            }
        } catch (ClientException $e) {
            error($e->getErrorMessage());
        } catch (ServerException $e) {
            error($e->getErrorMessage());
        }
    }

    public static function getErrorMessage($status)
    {
        $message    = [
            'isv.InvalidDayuStatus.Malformed'           => '账户短信开通状态不正确',
            'isv.InvalidSignName.Malformed'             => '短信签名不正确或签名状态不正确',
            'isv.InvalidTemplateCode.MalFormed'         => '短信模板Code不正确或者模板状态不正确',
            'isv.InvalidRecNum.Malformed'               => '目标手机号不正确，单次发送数量不能超过100',
            'isv.InvalidParamString.MalFormed'          => '短信模板中变量不是json格式',
            'isv.InvalidParamStringTemplate.Malformed'  => '短信模板中变量与模板内容不匹配',
            'isv.InvalidSendSms'                        => '触发业务流控',
            'isv.InvalidDayu.Malformed'                 => '变量不能是url，可以将变量固化在模板中'
        ];
        if (isset($message[$status])) {
            return $message[$status];
        }
        return $status;
    }
}
