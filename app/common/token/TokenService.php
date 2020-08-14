<?php

/**
 * Token相关控制器
 */

namespace app\common\token;

use app\BaseController;
use Firebase\JWT\JWT;
use think\facade\Env;

class TokenService extends BaseController
{
    /**
     * 获取token
     *
     * @param [type] $user_id
     * @return void
     */
    public static function getToken($user_id)
    {
        if (Env::get('APP.DOUBLE_TOKEN')) {
            $access_token_arr = [
                'user_id'     => $user_id,
                'expire_time' => strtotime('+2 hours') // access_token的过期时间为2小时
            ];
            $access_jwt_token = JWT::encode($access_token_arr, config('system.access_jwt_key'));
    
            $refresh_token_arr = [
                'user_id'     => $user_id,
                'expire_time' => strtotime('+1 month') // refresh_token的过期时间为1个月
            ];
            $refresh_jwt_token = JWT::encode($refresh_token_arr, config('system.refresh_jwt_key'));
    
            $jwt_data = [
                'access_jwt_token' => $access_jwt_token,
                'refresh_jwt_token' => $refresh_jwt_token,
            ];
        } else {
            $access_token_arr = [
                'user_id'     => $user_id,
                'expire_time' => strtotime('+10 day') // access_token的过期时间为10天
            ];
            $access_jwt_token = JWT::encode($access_token_arr, config('system.access_jwt_key'));
            $jwt_data = [
                'access_jwt_token' => $access_jwt_token,
            ];
        }
        
        return $jwt_data;
    }

    /**
     * 通过refresh_token获取access_token
     *
     * @param [type] $access_token
     * @param [type] $refresh_token
     * @return void
     */
    public static function getAccessTokenByRefreshToken($access_token, $refresh_token)
    {
        $access_jwt_key = config('system.access_jwt_key');
        $refresh_jwt_token = config('system.refresh_jwt_token');

        try {
            $access_token_arr = (array)JWT::decode($access_token, $access_jwt_key, array('HS256'));
            $refresh_jwt_arr = (array)JWT::decode($refresh_token, $refresh_jwt_token, array('HS256'));
        } catch (\UnexpectedValueException $exception) {
            error('Token Error', 403);
        }

        if (isset($access_token_arr['user_id']) && isset($access_token_arr['expire_time']) && isset($refresh_jwt_arr['user_id']) && isset($refresh_jwt_arr['expire_times'])) {
            if (time() > $access_token_arr['expire_time'] && time() < $refresh_jwt_token['expire_time']) {
                return self::getToken($refresh_jwt_token['useer_id']);
            } elseif (time() > $access_token_arr['expire_time'] && time() > $refresh_jwt_token['expire_time']) {
                error('登录认证过期', 403);
            } elseif (time() < $access_token_arr['expire_time'] && time() < $refresh_jwt_token['expire_time']) {
                return [$access_token, $refresh_token];
            }
        }
    }

    /**
     * 检验token
     *
     * @param [type] $token
     * @return void
     */
    public static function checkToken($token)
    {
        //判断token是否为非法的token
        $access_jwt_key = config('system.access_jwt_key');

        try {
            $token_arr = (array)JWT::decode($token, $access_jwt_key, array('HS256'));
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
                        return $token_arr['user_id'];
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
