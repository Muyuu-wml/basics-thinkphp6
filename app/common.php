<?php
// 应用公共文件

/**
 * 成功返回
 *
 * @param string $msg 返回文字信息
 * @param array $data 返回数据
 * @param integer $code 状态码
 * @return void
 */
function success($msg = 'success', $data = [], $code = 200)
{
    $result['code'] = $code;
    $result['msg'] = $msg;
    $result['data'] = $data;
    json($result)->send();
    exit();
}

/**
 * 错误返回
 *
 * @param string $msg 返回文字信息
 * @param array $data 返回数据
 * @param integer $code 状态码
 * @return void
 */
function error($msg = 'error', $data = [], $code = 400)
{
    $result['code'] = $code;
    $result['msg'] = $msg;
    $result['data'] = $data;
    json($result)->send();
    exit();
}

/**
 * 将任意变量转换成Array
 * @param $variate 任意类型变量
 * @param string $delimiter 分隔符
 */
function convertArray($variate, $delimiter = ',')
{
    //如果是数组，则直接返回
    if (is_array($variate)) return $variate;
    //如果是数字，则拼接返回
    if (is_numeric($variate)) return array($variate);
    //尝试转换成对象
    $objcet = simplexml_load_string($variate, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOERROR);
    //转换失败
    $array = $objcet === false ? json_decode($variate, true) : (array)$objcet;
    //尝试分割字符串
    return $array == null ? explode($delimiter, $variate) : $array;
}

/**
 * 数组转xml
 *
 * @param [type] $param 数组
 * @return void
 */
function convertXml($param)
{
    $xml = "<xml>";
    foreach ($param as $key => $value) {
        $xml .= (is_numeric($value)) ? "<" . $key . ">" . $value . "</" . $key . ">" : "<" . $key . "><![CDATA[" . $value . "]]></" . $key . ">";
    }
    $xml .= "</xml>";
    return $xml;
}

/**
 * 获取单机唯一id
 *
 * @return void
 */
function UUID(){
    $char_id = md5(uniqid(mt_rand(), true));
    $uuid = substr($char_id, 0, 8)
        .substr($char_id, 8, 4)
        .substr($char_id,12, 4)
        .substr($char_id,16, 4)
        .substr($char_id,20,12);
    return $uuid;
}

/**
 * 获取邀请码
 *
 * @return void
 */
function inviteCode()
{
    $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $rand = $code[rand(0, 25)]
        . strtoupper(dechex(date('m')))
        . date('d') . substr(time(), -5)
        . substr(microtime(), 2, 5)
        . sprintf('%02d', rand(0, 99));
    for (
        $a = md5($rand, true),
        $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV',
        $d = '',
        $f = 0;
        $f < 5;
        $g = ord($a[$f]),
        $d .= $s[($g ^ ord($a[$f + 8])) - $g & 0x1F],
        $f++
    );
    return $d;
}

/**
 * 生成密码盐
 *
 * @param integer $length 密码盐长度
 * @param [type] $chars 特定字符
 * @return void
 */
function salt($length = 8, $chars = null)
{
    if (empty($chars)) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    }
    $count = strlen($chars) - 1;
    $code = '';
    while (strlen($code) < $length) {
        $code .= substr($chars, rand(0, $count), 1);
    }
    return $code;
}

/**
 * 生成订单编号
 *
 * @return void
 */
function makeOrderNo(){
    //生成24位唯一订单号码，格式：YYYY-MMDD-HHII-SS-NNNN,NNNN-CC，其中：YYYY=年份，MM=月份，DD=日期，HH=24格式小时，II=分，SS=秒，NNNNNNNN=随机数，CC=检查码

    //订单号码主体（YYYYMMDDHHIISSNNNNNNNN）
    $order_id_main = date('YmdHis') . rand(10000000,99999999);

    //订单号码主体长度
    $order_id_len = strlen($order_id_main);
    $order_id_sum = 0;

    for($i=0; $i<$order_id_len; $i++){
        $order_id_sum += (int)(substr($order_id_main,$i,1));
    }

    //唯一订单号码（YYYYMMDDHHIISSNNNNNNNNCC）
    $order_id = $order_id_main . str_pad((100 - $order_id_sum % 100) % 100,2,'0',STR_PAD_LEFT);
    return $order_id;
}

/**
 * 处理登录密码加密
 *
 * @param [type] $password 密码
 * @param [type] $salt 密码盐
 * @param string $type 加密方式 默认md5
 * @return void
 */
function encryption($password, $salt, $type = 'md5')
{
    if ($type == 'md5') {
        return md5(md5($password . $salt));
    } else {
        return hash('sha1', $password . $salt);
    }
}

/**
 * http的get请求
 *
 * @param [type] $url 请求url
 * @param array $data 请求数据
 * @return void
 */
function http_get($url, $data = array()) {
    $curl = curl_init();
    if($data){
        $submit_url = $url;
    }else{
        //这里的$data 如果传递的是数组需要http_build_query($data)
        $submit_url = $url . '?' . http_build_query($data);
    }
    curl_setopt($curl, CURLOPT_URL, $submit_url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT,60);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

/**
 * http post请求
 *
 * @param [type] $url 请求url
 * @param array $data 请求数据
 * @param integer $is_json_post 是否转为json数据
 * @return void
 */
function http_post($url, $data = [], $is_json_post = 0){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    if($is_json_post){
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT,60);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}