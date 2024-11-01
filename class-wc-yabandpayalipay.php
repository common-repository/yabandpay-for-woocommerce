<?php
/**
 * @project    : YabandPay For WooCommerce
 * @description: YabandPay is the authorized payment partner of WeChat Pay in SEPA region, providing integrated cross border payment solution from CNY to Euro.
 * @user       : YabandPay
 * @email info@yabandmedia.com
 * @date       : 2018/6/19
 * @time       : 21:12
 */

class WC_YabandPayAliPay extends WC_YabandPay
{

    public $id = \YaBandPay\PersiLiao\Payment::METHOD_ALIPAY_ID;

    protected $pay_text = 'YabandPay Alipay Plus';

    protected $notify_url = 'WC_YabandPay_AliPay';

    protected $payment_method = \YaBandPay\PersiLiao\Payment::ALIPAY;

    public $title = 'Alipay Plus';

    public $payment_description='Low exchange rate, many concessions';
    
    public $description='Low exchange rate, many concessions';
}