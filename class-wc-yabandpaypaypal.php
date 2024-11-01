<?php
/**
 * @project    : YabandPay For WooCommerce
 * @description: YabandPay is the authorized payment partner of WeChat Pay in SEPA region, providing integrated cross border payment solution from CNY to Euro.
 * @user       : YabandPay
 * @email info@yabandmedia.com
 * @date       : 2018/6/19
 * @time       : 21:12
 */

class WC_YabandPayPayPal extends WC_YabandPay
{

    public $id = \YaBandPay\PersiLiao\Payment::METHOD_PAYPAL_ID;

    protected $pay_text = 'YabandPay PayPal';

    protected $notify_url = 'WC_YabandPay_PayPal';

    protected $payment_method = \YaBandPay\PersiLiao\Payment::PAYPAL;

    public $title = 'PayPal';

    public $payment_description='Pay easily with PayPal';
    
    public $description='Pay easily with PayPal';
}