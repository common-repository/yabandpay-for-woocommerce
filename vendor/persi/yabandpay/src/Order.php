<?php
/**
 * @project: YaBandPay-Composer
 * @description:
 * @user: PersiLiao
 * @email: persi@sixsir.com
 * @date: 2019/2/28
 * @time: 23:01
 */

namespace YaBandPay\PersiLiao;


use function number_format;

class Order
{
    public static function calculateAmount($amount, $fee)
    {
        if($amount <= 0){
            return 0;
        }
        if($fee <= 0){
            return $amount;
        }
        $amount += $amount * ($fee / 100);
        return number_format($amount, 2);
    }
}