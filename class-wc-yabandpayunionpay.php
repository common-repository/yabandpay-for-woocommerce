<?php
/**
 * @project    : YabandPay For WooCommerce
 * @description: YabandPay is the authorized payment partner of Union Pay in SEPA region, providing integrated cross border payment solution from CNY to Euro.
 * @user       : YabandPay
 * @email info@yabandmedia.com
 * @date       : 2020/6/3
 * @time       : 17:37
 */

class WC_YabandPayUnionPay extends WC_YabandPay
{
    public $id = \YaBandPay\PersiLiao\Payment::METHOD_UNIONPAY_ID;

    protected $pay_text = 'YabandPay UnionPay';

    protected $notify_url = 'WC_YabandPay_UnionPay';

    protected $payment_method = \YaBandPay\PersiLiao\Payment::UNIONPAY;

    public $title = 'UnionPay';

    public $payment_description='Pay with UnionPay cards and QuickPass';
    
    public $description='Pay with UnionPay cards and QuickPass';
}