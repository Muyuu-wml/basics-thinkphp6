<?php

namespace app\index\controller;

use app\BaseController;
use app\common\token\TokenService;

class Auth extends BaseController
{
    private $user_id;

    /**
     * 控制器初始化方法，用于总体控制用户是否登陆，验证token，以及权限控制
     */
    public function initialize()
    {
        // 设置请求方法
        header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
        // 设置跨域允许包含的头
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Referer, User-Agent, Authorization, X-Auth-Token, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since");
        //设置允许所有域访问
        header("Access-Control-Allow-Origin: *");
        if (request()->method() == 'OPTIONS') {
            // options 方法探测 header
            exit('ok');
        }

        //获取头部信息(存储头部token的地方)
        $token = request()->header('Authorization');
        if (empty($token)) {
            $token = input('Authorization');
        }

        //判断用户token是否过期或者是否非法
        if (empty($token)) {
            error('AccessToken is empty', 401);
        }

        $this->user_id = TokenService::checkToken($token);
    }

    /**
     * 获取user_id
     *
     * @return void
     */
    public function getUserId()
    {
        return $this->user_id;
    }
}
