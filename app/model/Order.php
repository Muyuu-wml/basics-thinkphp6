<?php

namespace app\model;

use think\Model;

class Order extends Model
{
    protected $autoWriteTimestamp = true;

    /**
     * 创建订单
     *
     * @param [type] $order_data 订单数据
     * @return void
     */
    public static function createOrder($order_data)
    {
        
    }
}