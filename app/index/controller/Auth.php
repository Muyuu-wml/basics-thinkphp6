<?php

namespace app\index\controller;

use app\BaseController;
use Firebase\JWT\JWT;

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
        $this->token = $token;

        //判断token是否为非法的token
        $key = config('system.access_jwt_key');
        try {
            $token_arr = (array)JWT::decode($token, $key, array('HS256'));
        } catch (\UnexpectedValueException $exception) {
            error('Invalid AccessToken', 401);
        }

        if (empty($token_arr)) {
            error('Invalid AccessToken', 401);
        } else {
            //判断token是否非法的
            if (isset($token_arr['user_id']) && isset($token_arr['expire_time'])) {
                if (!empty($token_arr['user_id']) && !empty($token_arr['expire_time'])) {
                    //判断时间是否过期
                    if (time() <= $token_arr['expire_time']) {
                        $this->user_id = $token_arr['user_id'];
                    } else {
                        error('登录认证过期', 402);
                    }
                } else {
                    error('Invalid AccessToken', 401);
                }
            } else {
                error('Invalid AccessToken', 401);
            }
        }
    }
}
