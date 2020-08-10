<?php

namespace app\pay;

use app\model\Order;
use think\Collection;

class PayNotify extends Collection
{
    // 支付回调
    public function notify()
    {
        $pay_status = input('pay_status');
        if ($pay_status == 'wxpay') {
            $post_xml = file_get_contents('php://input');
            $post_array = convertArray($post_xml);
            $out_trade_no = $post_array['out_trade_no'];
        } elseif ($pay_status == 'alipay') {
            $out_trade_no = input('out_trade_no');
        }

        // Order::notify($out_trade_no);
    }
}
