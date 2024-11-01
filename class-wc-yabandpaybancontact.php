<?php
/**
 * @project    : YabandPay For WooCommerce
 * @description: YabandPay is the authorized payment partner of WeChat Pay in SEPA region, providing integrated cross border payment solution from CNY to Euro.
 * @user       : YabandPay
 * @email info@yabandmedia.com
 * @date       : 2018/6/19
 * @time       : 21:12
 */

class WC_YabandPayBancontact extends WC_YabandPay
{

    public $id = \YaBandPay\PersiLiao\Payment::METHOD_BANCONTACT_ID;

    protected $pay_text = 'YabandPay Bancontact';

    protected $notify_url = 'WC_YabandPay_Bancontact';

    protected $payment_method = \YaBandPay\PersiLiao\Payment::BANCONTACT;

    public $title = 'Bancontact';

    public $payment_description='Pay easily with Bancontact';
    
    public $description='Pay easily with Bancontact';
}