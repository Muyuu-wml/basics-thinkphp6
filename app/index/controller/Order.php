<?php

namespace app\index\controller;

use app\index\validate\Order as OrderValidate;
use think\exception\ValidateException;
use app\model\Order as OrderModel;
use app\common\pay\PayService;

class Order extends Auth
{
    /**
     * 创建订单
     *
     * @return void
     */
    public function create()
    {
        $order_data = [
            'user_id'      => $this->getUserId(),
            'out_trade_no' => makeOrderNo(),
            'goods_id'     => input('goods_id'),
            'pay_type'     => input('pay_type'), // 支付方式（支付宝支付：alipay 微信支付：wxpay）
            'quantity'     => input('quantity', 1)
        ];

        try {
            validate(OrderValidate::class)->check($order_data);
        } catch (ValidateException $e) {
            error($e->getError());
        }

        $order_info = OrderModel::createOrder($order_data);

        if ($order_info) {
            $res = PayService::payment($order_info);
            success('创建订单成功', $res);
        } else {
            error('创建订单失败');
        }
    }

    /**
    * 支付回调
    *
    * @return void
    */
    public function notify()
    {
        $pay_type = input('pay_type');
        if ($pay_type == 'wxpay') {
            $post_xml = file_get_contents('php://input');
            $post_array = convertArray($post_xml);
            $out_trade_no = $post_array['out_trade_no'];
        } elseif ($pay_type == 'alipay') {
            $out_trade_no = input('out_trade_no');
        }
        // todo
        // Order::notify($out_trade_no);
    }

    /**
     * 获取个人订单列表
     *
     * @return void
     */
    public function orderList()
    {
        $where = [
            ['user_id', '=', $this->getUserId()]
        ];
        $order_list = OrderModel::getOrderList($where);
        success('订单列表', $order_list);
    }
}
