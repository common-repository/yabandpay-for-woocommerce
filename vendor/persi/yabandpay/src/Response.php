<?php
/**
 * @project    : yabandpay
 * @description:
 * @user       : PersiLiao
 * @email      : persi@sixsir.com
 * @date       : 2018/12/2
 * @time       : 22:39
 */

namespace YaBandPay\PersiLiao;


class Response
{
    const OK = 'ok';

    const BAD = 'bad';

    public static function ajaxReturnSuccess($content = 'ok')
    {
        echo $content;
        exit;
    }

    public static function ajaxReturnFailure($content = 'bad')
    {
        echo $content;
        exit;
    }
}