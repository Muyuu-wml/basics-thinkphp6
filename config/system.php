<?php
// +----------------------------------------------------------------------
// | 系统设置
// +----------------------------------------------------------------------

return [
    //jwt加密的key
    'access_jwt_key'    => 'access_jwt_key',

    // 阿里支付密钥
    'appid'         => '', // https://open.alipay.com 账户中心->密钥管理->开放平台密钥，填写添加了电脑网站支付的应用的APPID
    'rsaPrivateKey' => '', // 商户私钥，填写对应签名算法类型的私钥，如何生成密钥参考：https://docs.open.alipay.com/291/105971和https: //docs.open.alipay.com/200/105310
     
    // 微信支付密钥
    'partnerid' => '', // 微信支付商户号
    'appid'     => '', // 微信支付申请对应的公众号的APPID
    'appKey'    => '', // 微信支付申请对应的公众号的APP Key
    'apiKey'    => '', // https://pay.weixin.qq.com 帐户设置-安全设置-API安全-API密钥-设置API密钥

    // 阿里云短信
    'access_key_id' => '',
    'access_secret' => '',
    'sign_name'     => '',
    'template_code' => '',

    // 短信宝短信
    'dxb_username' => '',
    'dxb_password' => '',

    // 七牛云相关
    'qiniu_access_key'    => '',
    'qiniu_access_secret' => '',
    'qiniu_bucket'        => '',

];