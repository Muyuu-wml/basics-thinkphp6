<?php
namespace app\common\pay;

use app\BaseController;
use think\facade\Env;

class PayService extends BaseController
{
    /**
     * 吊起支付宝和微信支付
     *
     * @param [type] $order_info
     * @return void
     */
    public static function payment($order_info)
    {
        if ($order_info['pay_type'] == 'wxpay') {
            $mchid      = config('system.partnerid');
            $appid      = config('system.appid');
            $appKey     = config('system.appKey');
            $apiKey     = config('system.apiKey');
            $outTradeNo = $order_info['out_trade_no']; //你自己的商品订单号
            $payAmount  = $order_info['amount']; //付款金额，单位:元
            $orderName  = $order_info['goods_name']; //订单标题
            $notifyUrl  = Env::get('APP.DOMAIN_NAME').'/index/Order/notify/notify?pay_type=wxpay'; //付款成功后的回调地址(不要有问号)
            $returnUrl  = Env::get('APP.DOMAIN_NAME'); //付款成功后，页面跳转的地址
            $wapUrl     = Env::get('APP.DOMAIN_NAME'); //WAP网站URL地址
            $wapName    = Env::get('APP.APP_NAME'); //WAP 网站名
            /** 配置结束 */

            $wxPay = new WxpayService($mchid, $appid, $apiKey);
            $wxPay->setTotalFee($payAmount);
            $wxPay->setOutTradeNo($outTradeNo);
            $wxPay->setOrderName($orderName);
            $wxPay->setNotifyUrl($notifyUrl);
            $wxPay->setReturnUrl($returnUrl);
            $wxPay->setWapUrl($wapUrl);
            $wxPay->setWapName($wapName);

            $res = $wxPay->createJsBizPackage($payAmount, $outTradeNo, $orderName, $notifyUrl);
        } elseif ($order_info['pay_type'] == 'alipay') {
            $appid         = config('system.appid');
            $rsaPrivateKey = config('system.rsaPrivateKey');
            $notifyUrl     = Env::get('APP.DOMAIN_NAME').'/index/Order/notify?pay_status=alipay'; //付款成功后的异步回调地址
            $outTradeNo    = $order_info['out_trade_no']; //你自己的商品订单号，不能重复
            $payAmount     = $order_info['amount']; //付款金额，单位:元
            $orderName     = $order_info['goods_name']; //订单标题
            $signType      = 'RSA2'; //签名算法类型，支持RSA2和RSA，推荐使用RSA2
            /*** 配置结束 ***/

            $aliPay = new AlipayService();
            $aliPay->setAppid($appid);
            $aliPay->setNotifyUrl($notifyUrl);
            $aliPay->setRsaPrivateKey($rsaPrivateKey);
            $aliPay->setTotalFee($payAmount);
            $aliPay->setOutTradeNo($outTradeNo);
            $aliPay->setOrderName($orderName);
            $res = $aliPay->getOrderStr();
        }

        return $res;
    }
}