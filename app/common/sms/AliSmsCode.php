<?php
/**
 * 阿里云短信服务
 */
namespace app\common\sms;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class AliSmsCode implements BaseSmsCode
{
    public static function sendSmsCode($mobile, $sms_code)
    {
        $accessKeyId  = config('system.access_key_id');
        $accessSecret = config('system.access_secret'); //注意不要有空格
        $signName     = config('system.sign_name'); //配置签名
        $templateCode = config('system.template_code'); //配置短信模板编号
        // 短信模板变量替换JSON串,友情提示:如果JSON中需要带换行符,请参照标准的JSON协议。
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
                dd($opRes);
                return self::getErrorMessage($opRes['Code']);
            }
        } catch (ClientException $e) {
            error($e->getErrorMessage());
        } catch (ServerException $e) {
            error($e->getErrorMessage());
        }
    }

    public static function getErrorMessage($code)
    {
        $message = [
            'isp.RAM_PERMISSION_DENY'         => 'RAM权限DENY',
            'isv.OUT_OF_SERVICE'              => '业务停机',
            'isv.PRODUCT_UN_SUBSCRIPT'        => '未开通云通信产品的阿里云客户',
            'isv.PRODUCT_UNSUBSCRIBE'         => '产品未开通',
            'isv.ACCOUNT_NOT_EXISTS'          => '账户不存在',
            'isv.ACCOUNT_ABNORMAL'            => '账户异常',
            'isv.SMS_TEMPLATE_ILLEGAL'        => '短信模版不合法',
            'isv.SMS_SIGNATURE_ILLEGAL'       => '短信签名不合法',
            'isv.INVALID_PARAMETERS'          => '参数异常',
            'isv.MOBILE_NUMBER_ILLEGAL'       => '非法手机号',
            'isv.MOBILE_COUNT_OVER_LIMIT'     => '手机号码数量超过限制',
            'isv.TEMPLATE_MISSING_PARAMETERS' => '模版缺少变量',
            'isv.BUSINESS_LIMIT_CONTROL'      => '业务限流',
            'isv.INVALID_JSON_PARAM'          => 'JSON参数不合法，只接受字符串值',
            'isv.BLACK_KEY_CONTROL_LIMIT'     => '黑名单管控',
            'isv.PARAM_LENGTH_LIMIT'          => '参数超出长度限制',
            'isv.PARAM_NOT_SUPPORT_URL'       => '不支持URL',
            'isv.AMOUNT_NOT_ENOUGH'           => '账户余额不足',
            'isv.TEMPLATE_PARAMS_ILLEGAL'     => '模版变量里包含非法关键字'
        ];
        if (isset($message[$code])) {
            return $message[$code];
        }
        return $code;
    }
}
