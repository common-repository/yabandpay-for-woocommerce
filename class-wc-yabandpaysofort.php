<?php
/**
 * @project    : YabandPay For WooCommerce
 * @description: YabandPay is the authorized payment partner of WeChat Pay in SEPA region, providing integrated cross border payment solution from CNY to Euro.
 * @user       : YabandPay
 * @email info@yabandmedia.com
 * @date       : 2018/6/19
 * @time       : 21:12
 */

class WC_YabandPaySofort extends WC_YabandPay
{

    public $id = \YaBandPay\PersiLiao\Payment::METHOD_SOFORT_ID;

    protected $pay_text = 'YabandPay Sofort';

    protected $notify_url = 'WC_YabandPay_Sofort';

    protected $payment_method = \YaBandPay\PersiLiao\Payment::SOFORT;

    public $title = 'Sofort';
}