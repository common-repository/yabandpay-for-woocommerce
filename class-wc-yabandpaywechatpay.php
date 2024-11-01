<?php
/**
 * @project    : YabandPay For WooCommerce
 * @description: YabandPay is the authorized payment partner of WeChat Pay in SEPA region, providing integrated cross border payment solution from CNY to Euro.
 * @user       : YabandPay
 * @email info@yabandmedia.com
 * @date       : 2018/6/19
 * @time       : 21:12
 */

class WC_YabandPayWechatPay extends WC_YabandPay
{
    public $id = \YaBandPay\PersiLiao\Payment::METHOD_WECHATPAY_ID;

    protected $pay_text = 'YabandPay WeChat Pay';

    protected $notify_url = 'WC_YabandPay_WechatPay';

    protected $payment_method = \YaBandPay\PersiLiao\Payment::WECHAT;

    public $title = 'WeChat Pay';

    public $payment_description='Low exchange rate, many concessions';
    
    public $description='Low exchange rate, many concessions';
}