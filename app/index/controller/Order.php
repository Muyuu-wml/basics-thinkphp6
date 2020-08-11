<?php
namespace app\index\controller;

class Order extends Auth
{
    /**
     * 创建订单
     *
     * @return void
     */
    public function createOrder()
    {
        $order_data = [
            'user_id' => $this->user_id,
            
        ];
    }
}