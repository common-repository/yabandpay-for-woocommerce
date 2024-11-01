<?php
/**
 * @project    : YabandPay For WooCommerce
 * @description: YabandPay is the authorized payment partner of WeChat Pay in SEPA region, providing integrated cross border payment solution from CNY to Euro.
 * @user       : YabandPay
 * @email info@yabandmedia.com
 * @date       : 2018/6/19
 * @time       : 21:12
 */

class WC_YabandPayUnionPayCard extends WC_YabandPay
{

    public $id = \YaBandPay\PersiLiao\Payment::METHOD_UNIONPAYCARD_ID;

    protected $pay_text = 'YabandPay UnionPay Card';

    protected $notify_url = 'WC_YabandPay_UnionPayCard';

    protected $payment_method = \YaBandPay\PersiLiao\Payment::UNIONPAYCARD;

    public $title = 'UnionPay';

    public $payment_description='Pay with UnionPay cards and QuickPass';
    
    public $description='Pay with UnionPay cards and QuickPass';
}