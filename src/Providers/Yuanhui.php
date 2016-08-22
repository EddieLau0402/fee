<?php

namespace Eddie\Fee\Providers;

use Eddie\Fee\FeeInterface;
use Eddie\Fee\Util;

class Yuanhui implements FeeInterface
{
    use Util;


    /*
     * API uri
     */
    //// http://xxxxxxxx/API/MobileGet.ashx
    const API_MOBILE_GET = 'MobileGet.ashx'; // 话费充值


    /**
     * 服务地址
     *
     * @var
     */
    protected $_server;

    /**
     * 获取 API 校验
     *
     * @var
     */
    protected $_appkey;

    /**
     * 客户/账号
     *
     * @var
     */
    protected $_cid;

    /**
     * 资源
     *
     * @var
     */
    protected $_resource;

    /**
     * 订单流水号
     *
     * @var
     */
    protected $_order_id;

    /**
     * 手机号
     *
     * @var
     */
    protected $_mobile;

    /**
     * 充值金额
     *
     * @var
     */
    protected $_amount;


    /**
     * Yuanhui constructor.
     *
     * @author Eddie
     *
     * @param $config
     */
    public function __construct($config)
    {
        if (!is_array($config))
            throw new \Exception('请设置好参数并且配置参数必须是数组', 500);

        if (!$config['cid'])
            throw new \Exception('缺少cid参数', 500);

        if (!$config['appkey'])
            throw new \Exception('缺少appkey参数', 500);

        if (!$config['url'])
            throw new \Exception('缺少url参数', 500);


        $this->_server = $config['url'];
        $this->_appkey = $config['appkey'];
        $this->_cid = $config['cid'];
        $this->_resource = $config['resource'];
    }


    /**
     * Setter - set mobile.
     *
     * @author Eddie
     *
     * @param $mobile
     * @return $this
     */
    public function mobile($mobile)
    {
        $this->_mobile = $mobile;
        return $this;
    }

    /**
     * Setter - set order_id.
     *
     * @param $order_id
     * @return $this
     */
    public function orderId($order_id)
    {
        $this->_order_id = $order_id;
        return $this;
    }

    public function amount($amount)
    {
        if (!is_numeric($amount))
            throw new \Exception('请输入有效金额', 500);

        $this->_amount = $amount;
        return $this;
    }


    /**
     * 话费充值
     */
    public function recharge($amount = null)
    {
        if (!$this->_order_id) {
            throw new \Exception('订单号不能为空', 422);
        }
        if (!$this->_mobile) {
            throw new \Exception('手机号不能为空', 422);
        }
        if (!$this->_amount) {
            if (!$amount) {
                throw new \Exception('充值金额不能为空', 422);
            }
            $this->amount($amount);
        }

        $params = [
            'cid' => $this->_cid,
            'productid' => $this->__getProductId(),
            'orderid' => $this->_order_id,
            'mob' => $this->_mobile,
            'timestamps' => $this->__getMsec() // 精确到毫秒
        ];

        /*
         * 签名
         */
        $params['sign'] = $this->__signature($params);

        $url = $this->_server . self::API_MOBILE_GET;

        $response = $this->request($url, $params, 'POST');

        return $this->_parse($response);
    }

    /**
     * Parse response.
     *
     * @author Eddie
     *
     * @param $response
     * @return array $return
     */
    private function _parse($response)
    {
        $result = json_decode($response);

        if (!$result) return $result;

        $return = [
            'provider' => 'Yuanhui',
            'code'     => $result->Code,
            'msg'      => $result->Msg,
            'success'  => $result->Success
        ];
        if ($result->Code == '1001') {
            $return['order_sn'] = $result->OutOrderId;
        }

        return $return;
    }


    /**
     * Return signature string.
     *
     * 签名机制 :
     *     请求参数列表中，除sign外其他必填参数均需要参加验签;
     *     请求列表中的所有必填参数的参数值与APPKEY经过按值的字符串格式从小到大排序(字符串格式排序)后, 直接首尾相接连接为一个字符串,
     *     然后用md5指定的加密方式进行加密。
     *
     *
     * @author Eddie
     *
     * @param $params
     * @return string
     */
    private function __signature($params)
    {
        /*
         * 去除 非必选参数
         */
        //unset($params['recallurl']);

        /*
         * Generate signature.
         */
        $signArr = array_values($params);
        $signArr[] = $this->_appkey;
        sort($signArr, SORT_STRING);

        return strtoupper(md5(implode($signArr)));
    }

    /**
     * Get micro-seconds.
     *
     * @author Eddie
     *
     * @return bool|string
     */
    private function __getMsec()
    {
        list($msec, $sec) = explode(' ', microtime());

        return date('YmdHis' . (sprintf('%03d', $msec*1000)), $sec);
    }

    /**
     * Return productid.
     *
     * @author Eddie
     *
     * @return $productid
     */
    private function __getProductId()
    {
        if ($this->_amount) {
            $arr = array_flip($this->_resource);
            if (array_key_exists($this->_amount, $arr)) {
                return $arr[$this->_amount];
            }
            else {
                throw new \Exception('没有对应的资源!', 500);
            }
        }
    }

}
