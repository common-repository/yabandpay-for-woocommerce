<?php
/**
 * @project    : YabandPay For WooCommerce
 * @description: YabandPay is the authorized payment partner of Union Pay in SEPA region, providing integrated cross border payment solution from CNY to Euro.
 * @user       : YabandPay
 * @email info@yabandmedia.com
 * @date       : 2020/6/3
 * @time       : 17:37
 */

class WC_YabandPayUnionPayQuickPass extends WC_YabandPay
{
    public $id = \YaBandPay\PersiLiao\Payment::METHOD_UNIONPAYQUICKPASS_ID;

    protected $pay_text = 'YabandPay UnionPay QuickPass';

    protected $notify_url = 'WC_YabandPay_UnionPayQuickPass';

    protected $payment_method = \YaBandPay\PersiLiao\Payment::UNIONPAYQUICKPASS;

    public $title = 'UnionPay QuickPass';

    public $payment_description='Pay with UnionPay QuickPass';
    
    public $description='Pay with UnionPay QuickPass';
}