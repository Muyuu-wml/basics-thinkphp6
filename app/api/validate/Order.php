<?php

namespace app\api\validate;

use think\Validate;

class Order extends Validate
{
    protected $rule = [
        'goods_id' => ['require'],
        'pay_type' => ['require', 'in' => 'alipay,wxpay'],
    ];

    protected $message  =   [
        'goods_id.require' => '商品id不能位空',
        'pay_type.require' => '支付方式不能为空',
        'pay_type.in'      => '支付格式错误'
    ];
}