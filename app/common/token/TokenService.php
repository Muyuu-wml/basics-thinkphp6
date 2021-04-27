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
    /**获取token
     * @param $user_id
     * @param string $access_jwt_key
     * @return array
     */
    public static function getToken($user_id, $access_jwt_key = '')
    {
        if(empty($access_jwt_key)) {
            $access_jwt_key = config('system.access_jwt_key');
        }
        $time = time(); //当前时间
        $access_token_arr = [
            'iss' => '', //签发者 可选
            'aud' => '', //接收该JWT的一方，可选
            'iat' => $time, //签发时间
            'nbf' => $time , //(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
            'exp' => strtotime('+1 month'), //过期时间,这里设置一个月
            'data' => [ //自定义信息，不要定义敏感信息
                'user_id' => $user_id,
            ]
        ];
        $access_jwt_token = JWT::encode($access_token_arr, $access_jwt_key);
        $jwt_data = [
            'access_jwt_token' => $access_jwt_token,
        ];
        
        return $jwt_data;
    }

    /**检验token
     * @param $token
     * @param string $access_jwt_key
     * @return array
     */
    public static function checkToken($access_token, $access_jwt_key = '')
    {
        //判断token是否为非法的token
        if (empty($access_jwt_key)) {
            $access_jwt_key = config('system.access_jwt_key');
        }

        try {
            JWT::$leeway = 60;//当前时间减去60，把时间留点余地
            $decoded = JWT::decode($access_token, $access_jwt_key, ['HS256']); //HS256方式，这里要和签发的时候对应
            $arr = (array)$decoded;
            return ['state' => true, 'msg' => 'Valid AccessToken', 'user_id' => $arr['user_id']];
        } catch(\Firebase\JWT\SignatureInvalidException $e) {  //签名不正确
            return ['state' => false, 'msg' => $e->getMessage()];
        }catch(\Firebase\JWT\BeforeValidException $e) {  // 签名在某个时间点之后才能用
            return ['state' => false, 'msg' => $e->getMessage()];
        }catch(\Firebase\JWT\ExpiredException $e) {  // token过期
            return ['state' => false, 'msg' => '登录状态过期请重新登录'];
        }catch(Exception $e) {  //其他错误
            return ['state' => false, 'msg' => $e->getMessage()];
        }
    }
}
