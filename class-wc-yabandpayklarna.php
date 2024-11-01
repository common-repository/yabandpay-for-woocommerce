<?php
/**
 * @project    : YabandPay For WooCommerce
 * @description: YabandPay is the authorized payment partner of WeChat Pay in SEPA region, providing integrated cross border payment solution from CNY to Euro.
 * @user       : YabandPay
 * @email info@yabandmedia.com
 * @date       : 2018/6/19
 * @time       : 21:12
 */


class WC_YabandPayKlarna extends WC_YabandPay
{

    public $id = \YaBandPay\PersiLiao\Payment::METHOD_KLARNA_ID;

    protected $pay_text = 'YabandPay Klarna';

    protected $notify_url = 'WC_YabandPay_Klarna';

    protected $payment_method = \YaBandPay\PersiLiao\Payment::KLARNA;

    public $title = 'Klarna';

    public $payment_description='Pay easily with Klarna';
    
    public $description='Pay easily with Klarna';

}

