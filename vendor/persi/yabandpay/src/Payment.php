<?php
/**
 * @project    : yabandpay
 * @description:
 * @user       : PersiLiao
 * @email      : persi@sixsir.com
 * @date       : 2018/12/2
 * @time       : 22:39
 */

namespace YaBandPay\PersiLiao;


class Payment
{
    const LOG_TABLE_NAME = 'yabandpay_log';


    const METHOD_WECHATPAY_ID = 'yabandpay_wechatpay';

    const METHOD_ALIPAY_ID = 'yabandpay_alipay';

    const METHOD_UNIONPAY_ID = 'yabandpay_unionpay';

    const METHOD_UNIONPAYWAP_ID = 'yabandpay_unionpaywap';

    const METHOD_UNIONPAYCARD_ID = 'yabandpay_unionpaycard';

    const METHOD_UNIONPAYQUICKPASS_ID = 'yabandpay_unionpayquickpass';
    
    const METHOD_IDEAL_ID = 'yabandpay_ideal';
    
    const METHOD_KLARNA_ID = 'yabandpay_klarna';
    
    const METHOD_BANCONTACT_ID = 'yabandpay_bancontact';
    
    const METHOD_VISA_ID = 'yabandpay_visa';
    
    const METHOD_MASTERCARD_ID = 'yabandpay_mastercard';
    
    const METHOD_PAYPAL_ID = 'yabandpay_paypal';

    const WECHAT = 'WeChat Pay';

    const ALIPAY = 'Alipay';

    const UNIONPAY = 'UnionPay';

    const UNIONPAYWAP = 'UnionPayWap';

    const UNIONPAYCARD = 'UnionPay Card';

    const UNIONPAYQUICKPASS = 'UnionPay QuickPass';

    const IDEAL = 'iDeal';

    const KLARNA = 'SOFORT/DIGITAL';

    const BANCONTACT = 'Bancontact';
    
    const VISA = 'Visa';
    
    const MASTERCARD = 'MasterCard';

    const PAYPAL = 'PayPal/recurring';

    const PAY_NEW = 'new';

    const PAY_PENDING = 'pending';

    const PAY_PROCESSING = 'processing';

    const PAY_PAID = 'paid';

    const PAY_CANCELLED = 'canceled';

    const PAY_FAILED = 'failed';

    const PAY_REFUNDED = 'refunded';

    const PAY_EXPIRED = 'expired';

    const PAY_COMPLETED = 'completed';

    const META_TRADE_ID = 'yabandpay_trade_id';

    const META_TRANSACTION_ID = 'yabandpay_transaction_id';
    /**
     * @var string
     */
    private $type;

    private static $methods = array(
        self::METHOD_ALIPAY_ID,
        self::METHOD_WECHATPAY_ID
    );

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return array
     */
    public static function getMethods()
    {
        return self::$methods;
    }

    /**
     * @param array $methods
     */
    public static function setMethods($methods)
    {
        self::$methods = $methods;
    }
}
