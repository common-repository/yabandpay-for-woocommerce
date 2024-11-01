<?php
/**
 * @project    : yabandpay
 * @description:
 * @user       : PersiLiao
 * @email      : persi@sixsir.com
 * @date       : 2018/12/3
 * @time       : 00:28
 */

namespace YaBandPay\PersiLiao;

class Currency
{
    const EUR = 'EUR';
    const CNY = 'CNY';
    const GBP = 'GBP';
    const CHF = 'CHF';
    const DKK = 'DKK';
    const SEK = 'SEK';

    const CURRENCY_EXCHANGE = 'CURRENCY_EXCHANGE';

    public static function isSupported($currency)
    {
        $supported = [self::CNY, self::EUR, self::GBP, self::CHF, self::DKK, self::SEK];

        $currency = \strtoupper(\trim($currency));
        return \in_array($currency, $supported);
    }

    public static function calculateToEur($amount, $exchange_rate)
    {
        return \strval(\round($amount / $exchange_rate, 2));
    }
}
