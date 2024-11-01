<?php
/**
 * @project    : YabandPay For WooCommerce
 * @description: YabandPay is the authorized payment partner of Union Pay in SEPA region, providing integrated cross border payment solution from CNY to Euro.
 * @user       : YabandPay
 * @email info@yabandmedia.com
 * @date       : 2020/6/3
 * @time       : 17:37
 */

class WC_YabandPayiDeal extends WC_YabandPay
{
    public $id = \YaBandPay\PersiLiao\Payment::METHOD_IDEAL_ID;

    protected $pay_text = 'YabandPay iDEAL';

    protected $notify_url = 'WC_YabandPay_iDeal';

    protected $payment_method = \YaBandPay\PersiLiao\Payment::IDEAL;

    public $title = 'iDEAL';

    public $payment_description='Support all banks in the Netherlands test';
    
    public $description='Support all banks in the Netherlands';
}