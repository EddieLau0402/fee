<?php

return [
    /*
     * 源慧API - config
     */
    'yuanhui' => [
        /*
         * 客户/账号
         */
        'cid' => env('YUANHUI_CID', ''),

        /*
         * 获取 API 校验
         */
        'appkey' => env('YUANHUI_APP_KEY', ''),

        /*
         * 服务地址
         */
        'url' => env('YUANHUI_API_DOMAIN', 'http://i.eswapi.com/API/'),

        /*
         * 资源 :
         */
        'resource' => [ // productid(资源ID) => 奖品资源名称
            '10001001' => 2,
            '10001002' => 5,
            '10001003' => 50,
            '10001004' => 100,
            '10001005' => 1,
            '10001006' => 20,
            '10001007' => 30,
            '10001008' => 10,
        ],

    ],
];