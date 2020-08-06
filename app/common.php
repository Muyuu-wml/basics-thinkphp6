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
    return json($result);
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
    return json($result);
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
