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
function convert_array($variate, $delimiter = ',')
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
function convert_xml($param)
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
function UUID()
{
    $char_id = md5(uniqid(mt_rand(), true));
    $uuid = substr($char_id, 0, 8)
        . substr($char_id, 8, 4)
        . substr($char_id, 12, 4)
        . substr($char_id, 16, 4)
        . substr($char_id, 20, 12);
    return $uuid;
}

/**
 * 获取邀请码
 *
 * @return void
 */
function invite_code()
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
function make_order_no()
{
    //生成24位唯一订单号码，格式：YYYY-MMDD-HHII-SS-NNNN,NNNN-CC，其中：YYYY=年份，MM=月份，DD=日期，HH=24格式小时，II=分，SS=秒，NNNNNNNN=随机数，CC=检查码

    //订单号码主体（YYYYMMDDHHIISSNNNNNNNN）
    $order_id_main = date('YmdHis') . rand(10000000, 99999999);

    //订单号码主体长度
    $order_id_len = strlen($order_id_main);
    $order_id_sum = 0;

    for ($i = 0; $i < $order_id_len; $i++) {
        $order_id_sum += (int)(substr($order_id_main, $i, 1));
    }

    //唯一订单号码（YYYYMMDDHHIISSNNNNNNNNCC）
    $order_id = $order_id_main . str_pad((100 - $order_id_sum % 100) % 100, 2, '0', STR_PAD_LEFT);
    return $order_id;
}

/**
 * 处理登录密码加密
 *
 * @param [type] $password 密码
 * @param [type] $salt 密码盐
 * @return void
 */
function encryption($password, $salt = '')
{
    return md5(md5($password . $salt));
}

/**
 * http的get请求
 *
 * @param [type] $url 请求url
 * @param array $data 请求数据
 * @return void
 */
function http_get($url, $data = array())
{
    $curl = curl_init();
    if ($data) {
        $submit_url = $url;
    } else {
        //这里的$data 如果传递的是数组需要http_build_query($data)
        $submit_url = $url . '?' . http_build_query($data);
    }
    curl_setopt($curl, CURLOPT_URL, $submit_url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_TIMEOUT, 60);
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
function http_post($url, $data = [], $json = 0)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    if (!empty($data)) {
        if ($json && is_array($data)) {
            $data = json_encode($data);
        }
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        if ($json) {
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json; charset=utf-8',
                'Content-Length:' . strlen($data)
            ]);
        }
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $res = curl_exec($curl);
    $errorno  = curl_errno($curl);
    if ($errorno) {
        return $errorno;
    }
    curl_close($curl);
    return $res;
}

/**
 * 获取客户端IP地址
 *
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return void
 */
function get_client_ip($type = 0, $adv = false)
{
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) unset($arr[$pos]);
            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * 时间差
 *
 * @param [type] $time 时间戳
 * @return void
 */
function mdate($time = NULL)
{
    $rtime = date("m-d H:i", $time);
    $htime = date("H:i", $time);

    $time = time() - $time;

    if ($time < 60) {
        $str = '刚刚';
    } elseif ($time < 60 * 60) {
        $min = floor($time / 60);
        $str = $min . '分钟前';
    } elseif ($time < 60 * 60 * 24) {
        $h = floor($time / (60 * 60));
        $str = $h . '小时前 ' . $htime;
    } elseif ($time < 60 * 60 * 24 * 3) {
        $d = floor($time / (60 * 60 * 24));
        if ($d == 1)
            $str = '昨天 ' . $rtime;
        else
            $str = '前天 ' . $rtime;
    } else {
        $str = $rtime;
    }
    return $str;
}

/**
 * 微信信息解密
 * @param string $appid 小程序id
 * @param string $sessionKey 小程序密钥
 * @param string $encryptedData 在小程序中获取的encryptedData
 * @param string $iv 在小程序中获取的iv
 * @return array 解密后的数组
 */
function decryptData($appid, $sessionKey, $encryptedData, $iv)
{
    $OK = 0;
    $IllegalAesKey = -41001;
    $IllegalIv = -41002;
    $IllegalBuffer = -41003;
    $DecodeBase64Error = -41004;

    if (strlen($sessionKey) != 24) {
        return $IllegalAesKey;
    }
    $aesKey = base64_decode($sessionKey);

    if (strlen($iv) != 24) {
        return $IllegalIv;
    }
    $aesIV = base64_decode($iv);

    $aesCipher = base64_decode($encryptedData);

    $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
    $dataObj = json_decode($result);
    if ($dataObj == NULL) {
        return $IllegalBuffer;
    }
    if ($dataObj->watermark->appid != $appid) {
        return $DecodeBase64Error;
    }
    return json_decode($result, true);
}

/**
 * 获取树状图
 *
 * @param [type] $arr 要处理的数组
 * @param integer $pid 父id
 * @param string $id id名称
 * @param string $pname pid名称
 * @param string $child 子级key名称
 * @param integer $level level
 * @return void
 */
function getTree($arr, $pid = 0, $id = 'id', $pname = 'pid', $child = 'child', $level = 1)
{
    $tree = array();
    foreach ($arr as $value) {
        if ($value[$pname] == $pid) {
            $value[$child] = getTree($arr, $value[$id], $id = $id, $pname = $pname, $child = $child, $level + 1);
            if ($value[$child] == null) {
                unset($value[$child]);
            }
            $value['level'] = $level;
            $tree[] = $value;
        }
    }
    return $tree;
}
