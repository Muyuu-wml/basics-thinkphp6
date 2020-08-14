<?php
namespace app\common\pay;

class AlipayService
{
    protected $appId;
    protected $notifyUrl;
    protected $charset;
    //私钥值
    protected $rsaPrivateKey;
    protected $totalFee;
    protected $outTradeNo;
    protected $orderName;
    public function __construct()
    {
        $this->charset = 'utf-8';
    }
    public function setAppid($appid)
    {
        $this->appId = $appid;
    }
    public function setNotifyUrl($notifyUrl)
    {
        $this->notifyUrl = $notifyUrl;
    }
    public function setRsaPrivateKey($saPrivateKey)
    {
        $this->rsaPrivateKey = $saPrivateKey;
    }
    public function setTotalFee($payAmount)
    {
        $this->totalFee = $payAmount;
    }
    public function setOutTradeNo($outTradeNo)
    {
        $this->outTradeNo = $outTradeNo;
    }
    public function setOrderName($orderName)
    {
        $this->orderName = $orderName;
    }
    /**
     * 获取orderStr
     * @return array
     */
    public function getOrderStr()
    {
        //请求参数
        $requestConfigs = array(
            'out_trade_no'=>$this->outTradeNo,
            'total_amount'=>$this->totalFee, //单位 元
            'subject'=>$this->orderName,  //订单标题
            'product_code'=>'QUICK_MSECURITY_PAY', //销售产品码，商家和支付宝签约的产品码，为固定值QUICK_MSECURITY_PAY
            'timeout_express'=>'2h',       //该笔订单允许的最晚付款时间，逾期将关闭交易。取值范围：1m～15d。m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）。 该参数数值不接受小数点， 如 1.5h，可转换为 90m。
//            'store_id'=>'',                 //商户门店编号。该参数用于请求参数中以区分各门店，非必传项。
//            'extend_params'=>array(
//                'sys_service_provider_id'=>''       //系统商编号，该参数作为系统商返佣数据提取的依据，请填写系统商签约协议的PID
//            )
        );
        $commonConfigs = array(
            //公共参数
            'app_id' => $this->appId,
            'method' => 'alipay.trade.app.pay',             //接口名称
            'format' => 'JSON',
            'charset'=>$this->charset,
            'sign_type'=>'RSA2',
            'timestamp'=>date('Y-m-d H:i:s'),
            'version'=>'1.0',
            'notify_url' => $this->notifyUrl,
            'biz_content'=>json_encode($requestConfigs),
        );
        $commonConfigs["sign"] = $this->generateSign($commonConfigs, $commonConfigs['sign_type']);
        $result = $this->buildOrderStr($commonConfigs);
        return $result;
    }
    public function generateSign($params, $signType = "RSA") {
        return $this->sign($this->getSignContent($params), $signType);
    }
    protected function sign($data, $signType = "RSA") {
        $priKey=$this->rsaPrivateKey;
        $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($priKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";
        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');
        if ("RSA2" == $signType) {
            openssl_sign($data, $sign, $res, version_compare(PHP_VERSION,'5.4.0', '<') ? SHA256 : OPENSSL_ALGO_SHA256); //OPENSSL_ALGO_SHA256是php5.4.8以上版本才支持
        } else {
            openssl_sign($data, $sign, $res);
        }
        $sign = base64_encode($sign);
        return $sign;
    }
    /**
     * 校验$value是否非空
     *  if not set ,return true;
     *    if is null , return true;
     **/
    protected function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;
        return false;
    }
    public function getSignContent($params) {
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {
                // 转换成目标字符集
                $v = $this->characet($v, $this->charset);
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }
        unset ($k, $v);
        return $stringToBeSigned;
    }
    /**
     * 转换字符集编码
     * @param $data
     * @param $targetCharset
     * @return string
     */
    function characet($data, $targetCharset) {
        if (!empty($data)) {
            $fileType = $this->charset;
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
                //$data = iconv($fileType, $targetCharset.'//IGNORE', $data);
            }
        }
        return $data;
    }

    public function buildOrderStr($data)
    {
        return http_build_query($data);
    }
}
// 是否支付宝客户端
function isInAlipayClient() {
    if( strpos($_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false ) {
        return true;
    }
    return false;
}
function getCurrentUrl()
{
    $scheme = $_SERVER['HTTPS']=='on' ? 'https://' : 'http://';
	$uri = $_SERVER['PHP_SELF'].$_SERVER['QUERY_STRING'];
	if($_SERVER['REQUEST_URI']) $uri = $_SERVER['REQUEST_URI'];
	$baseUrl = urlencode($scheme.$_SERVER['HTTP_HOST'].$uri);
    return $baseUrl;
}