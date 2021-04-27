<?php

namespace app\api\controller;

use app\BaseController;
use app\common\token\TokenService;
use app\Request;

class Auth extends BaseController
{
    private $user_id;

    private $white_list = [];

    /**
     * 控制器初始化方法，用于总体控制用户是否登陆，验证token，以及权限控制
     *
     * @return void
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
        $controller = request()->controller();
        $action = request()->action();

        if (!empty($token)) {
            $res = TokenService::checkToken($token);
            if ($res['state'] == true || in_array("{$controller}/{$action}", $this->white_list)) {
                $this->user_id = $res['user_id'];
            } else {
                error($res['msg'], [], 401);
            }
        } elseif(!in_array("{$controller}/{$action}", $this->white_list)) {
            error('请先登录', [], 401);
        }
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
