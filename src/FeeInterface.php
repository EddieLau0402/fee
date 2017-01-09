<?php

namespace Eddie\Fee;


interface FeeInterface
{
    /**
     * 手机号
     *
     * @param $mobile
     * @return mixed
     */
    public function mobile($mobile);

    /**
     * 充值,应该返回标准格式
     *
     * @return array ['success'=>boolean,'order_sn'=>string,'provider'=>string,'code'=>int, 'msg'=>string]
     */
    public function recharge();
}