<?php
/**
 * @project    : YabandPay For WooCommerce
 * @description: YabandPay is the authorized payment partner of WeChat Pay in SEPA region, providing integrated cross border payment solution from CNY to Euro.
 * @user       : YabandPay
 * @email info@yabandmedia.com
 * @date       : 2018/6/19
 * @time       : 21:12
 */

class WC_YabandPayUnionPayWap extends WC_YabandPay
{

    public $id = \YaBandPay\PersiLiao\Payment::METHOD_UNIONPAYWAP_ID;

    protected $pay_text = 'YabandPay UnionPay Wap';

    protected $notify_url = 'WC_YabandPay_UnionPayWap';

    protected $payment_method = \YaBandPay\PersiLiao\Payment::UNIONPAYWAP;

    public $title = 'UnionPay';

    public $payment_description='Pay with UnionPay cards and QuickPass';
    
    public $description='Pay with UnionPay cards and QuickPass';
}