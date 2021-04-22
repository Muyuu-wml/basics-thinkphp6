<?php
namespace app\admin\controller;

use app\BaseController;
use app\common\token\TokenService;
use app\Request;

class Auth extends BaseController
{
    private $user_id;

    /**
     * 控制器初始化方法，用于总体控制用户是否登陆，验证token，以及权限控制
     */
    public function initialize()
    {
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
            error('AccessToken is empty', [], 401);
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