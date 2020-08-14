<?php

namespace app\model;

use think\Model;
use app\model\Goods;
use app\model\AccountDetail;

class Order extends Model
{
    protected $autoWriteTimestamp = true;

    /**
     * 创建订单
     *
     * @param [type] $order_data 订单数据
     * @return void
     */
    public static function createOrder(array $order_data)
    {
        $goods_info = Goods::where('id', $order_data['goods_id'])->where('delete_time', null)->find();
        if ($goods_info) {
            
            $order_data['amount'] = $goods_info['amount'] * $order_data['quantity'];
            $order_data['goods_name'] = $goods_info['name'];
            self::startTrans();
            try {
                // 记录消费记录
                AccountDetail::create(['user_id' => $order_data['user_id'], 'describe' => $order_data['goods_name'], 'record' => $order_data['amount']]);
                
                // 商品相关的业务逻辑

                // 创建订单 
                self::create($order_data);
                self::commit();
                return $order_data;
            } catch (\Exception $e) {
                self::rollback();
                error('数据库内部错误');
            }
        } else {
            error('商品不存在');
        }
    }

    /**
     * 支付回调
     *
     * @param [type] $out_trade_no 订单号
     * @return void
     */
    public static function notify($out_trade_no)
    {
        $order = self::where('out_trade_no', $out_trade_no)->where('status', 0)->where('delete_time', null)->find();
        if ($order) {
            try {
                $order->status = 1;
                $order->save();
                return true;
            } catch (\Exception $e) {
                error('数据库内部错误');
            }
        } else {
            error('没有此订单');
        }
    }

    /**
     * 用户订单列表
     *
     * @param [type] $user_id
     * @return void
     */
    public static function getOrderList($where)
    {
        try {
            $order_list = self::field('id, user_id, out_trade_no, goods_id, goods_name, amount, status, pay_type, create_time')->where($where)->where('delete_time', null)->paginate(10);
            return $order_list;
        } catch (\Exception $e) {
            error('数据库内部错误');
        }
    }
}