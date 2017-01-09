<?php

namespace Eddie\Fee;


use Eddie\Fee\Providers\Yuanhui;

class FeeManager
{
    public function provider($provider)
    {
        switch (strtolower($provider)) {
            case 'yuanhui':
                $config = config('fee.yuanhui');
                return new Yuanhui($config);

            default:
                throw new \Exception('找不到相应的provider', 500);
        }
    }
}