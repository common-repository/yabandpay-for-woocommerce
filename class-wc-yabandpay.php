<?php
/**
 * @project    : YaBandPay-Wordpress
 * @description:
 * @user       : PersiLiao
 * @email      : persi@sixsir.com
 * @date       : 2018/12/3
 * @time       : 13:52
 */

use YaBandPay\PersiLiao\Account;
use YaBandPay\PersiLiao\Api;
use YaBandPay\PersiLiao\Cryptography;
use YaBandPay\PersiLiao\Currency;
use YaBandPay\PersiLiao\Notify;
use YaBandPay\PersiLiao\Order;
use YaBandPay\PersiLiao\Payment;
use YaBandPay\PersiLiao\Request;
use YaBandPay\PersiLiao\Response;

abstract class WC_YabandPay extends WC_Payment_Gateway
{
    const PAY_PENDING = 'pending';

    const PAY_PROCESSING = 'processing';

    const PAY_PAID = 'paid';

    const PAY_CANCELLED = 'canceled';

    const PAY_FAILED = 'failed';

    const PAY_REFUNDED = 'refunded';

    const PAY_EXPIRED = 'expired';

    const PAY_COMPLETED = 'completed';

    public $method_title;

    protected $pay_text;

    protected $charset = 'utf-8';

    protected $debug = false;

    protected $notify_url;

    protected $domain = 'yabandpay';

    protected $current_currency;

    protected $exchange_rate;
    /**
     * @var int
     */
    protected $fee = 0;
    /**
     * @var array
     */
    protected $supported_currencies = array( Currency::CNY, Currency::EUR );

    protected $order_pay_status;

    public $order_button_text;
    /**
     * @var string
     */
    protected $yabandpay_account;
    /**
     * @var string
     */
    protected $yabandpay_token;

    protected $payment_method;

    protected $request;

    protected $cryptography;

    public $method_description;

    protected $option_prefix_key = null;

    protected $enable_key = 'enabled';
    
    protected $yabandpay_debug;

    protected static $order_pay_status_list = [
        Payment::PAY_PROCESSING,
        Payment::PAY_PAID,
        Payment::PAY_CANCELLED,
        Payment::PAY_FAILED,
        Payment::PAY_REFUNDED,
        Payment::PAY_EXPIRED,
    ];

    public function __construct()
    {
        $this->multi_currency_enabled = \in_array(
                'woocommerce-multilingual/wpml-woocommerce.php',
                \apply_filters('active_plugins', \get_option('active_plugins'))
            ) && \get_option('icl_enable_multi_currency') == 'yes';

        if(self::isLang('zh_CN')){
            $this->icon = \apply_filters('woocommerce_yabandpay_icon', \plugins_url('images/' . $this->id . '_en.png', __FILE__));
        }else{
            $this->icon = \apply_filters('woocommerce_yabandpay_icon', \plugins_url('images/' . $this->id . '_en.png', __FILE__));
        }

        $this->has_fields = false;
        $this->enabled = $this->get_option($this->getEnableKey());
        $this->setOptionPrefixKey();
        $this->setCurrentCurrency();
        $this->setMethodTitle();
        $this->setMethodDescription();
        $this->setOrderButtonText();
        $this->init_form_fields();
        $this->init_settings();
        $this->setTitle();
        $this->setDescription();
        $this->setYabandpayAccount();
        $this->setYabandpayToken();
        $this->setExchangeRate();
        $this->setNotifyUrl();
        $this->setYabandpayDebug();
        $this->form_submission_method = $this->get_option('form_submission_method') == 'yes' ? true : false;
        //$this->debug = $this->get_option('yabandpay_debug') == 'yes' ? true : false;

        // $this->title       = $this->get_option('Title');
        // $this->description = $this->get_option('description');

        \add_action('woocommerce_update_options_payment_gateways', [ $this, 'process_admin_options' ]); // WC <= 1.6.6
        \add_action('woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ]);

        \add_filter('woocommerce_valid_order_statuses_for_payment', function($status, WC_Order $order){
            if(isset($status, $order)){
            }
            return [ self::PAY_FAILED, self::PAY_EXPIRED, self::PAY_PENDING ];
        }, 10, 2);
        \add_filter('woocommerce_order_needs_payment', function($pay_status, WC_Order $order, $status){
            if($pay_status){
            }
            $order_status = $order->get_status();
            if(\in_array($order_status, $status, true)){
                return true;
            }
            return false;
        }, 10, 3);
        \add_filter('woocommerce_valid_order_statuses_for_payment_complete', function(array $status_list, WC_Order $order){
            if(isset($status_list)){
            }
            $order_status = $order->get_status();
            if($order_status === self::PAY_PENDING){
                return [ self::PAY_PENDING ];
            }elseif($order_status === self::PAY_PROCESSING){
                return [ self::PAY_PROCESSING ];
            }
            return [ $order_status ];
        }, 10, 2);

        \add_action('woocommerce_cancelled_order', function($order_id){
            return $this->cancelPayment($order_id);
        }, 10, 1);
        \add_action('woocommerce_update_options_checkout_' . $this->id, array( $this, 'yabandpayOptionsUpdateAfterAction' ));
    }

    public function yabandpayOptionsUpdateAfterAction()
    {
        if(self::isCheckAccountAction()){
            return true;
        }
        self::setCheckAccountAction();
        $yabandpay_account = \trim($this->getOptionMetaValue('yabandpay_account'));
        $yabandpay_token = \trim($this->getOptionMetaValue('yabandpay_token'));
        //$desc = \trim($this->getOptionMetaValue('payment_desc'));
        $desc = \trim($this->getOptionMetaValue('description'));
        $this->updateYabandPayOptions($yabandpay_account, $yabandpay_token, $desc);
        if(!isset($yabandpay_account) || empty($yabandpay_account) || !isset($yabandpay_token) || empty($yabandpay_token)){
            $this->disableAllPaymentMethods();
            self::addErrorNotice(__('Account or Token can not be empty.', $this->domain));
            return false;
        }
        // try{
        //     /*$cryptography = new Cryptography($yabandpay_token);
        //     $request = new Request($cryptography);
        //     $api = new Api(new Account($yabandpay_account, $yabandpay_token), $request);
        //     $api->verify();*/
        //     if(Currency::isSupported($this->getCurrentCurrency()) === false){
        //         $exchange_rate = \trim($this->getOptionMetaValue('exchange_rate'));
        //         $this->updateYabandPayOptions($yabandpay_account, $yabandpay_token, $desc, $exchange_rate);
        //         if(empty($exchange_rate) || $exchange_rate <= 0){
        //             $this->disableAllPaymentMethods();
        //             self::addErrorNotice(__('Please enter the Exchange Rate.', $this->domain));
        //             return false;
        //         }
        //     }
            return true;    //这个是原本就在这个try代码块的
        // }catch(\Exception $e){
        //     $this->log(__FUNCTION__ . ':exception:' . $e->getMessage());
        //     $this->disableAllPaymentMethods();
        //     self::addErrorNotice(__('Account or Token invalid.', $this->domain));
        //     return false;
        // }
    }

    protected function disableAllPaymentMethods($enable = 'no')
    {
        /**
         * @var WC_Payment_Gateway $gateway
         */
        foreach(WC()->payment_gateways()->payment_gateways() ?: array() as $gateway){
            if ((!$gateway instanceof WC_YabandPayWechatPay)
                && (!$gateway instanceof WC_YabandPayAliPay)
                && (!$gateway instanceof WC_YabandPayiDeal)
                && (!$gateway instanceof WC_YabandPayKlarna)
                && (!$gateway instanceof WC_YabandPayBancontact)
                && (!$gateway instanceof WC_YabandPayVisa)
                && (!$gateway instanceof WC_YabandPayMastercard)
                && (!$gateway instanceof WC_YabandPayPayPal)
                && (!$gateway instanceof WC_YabandPayUnionPay)
                && (!$gateway instanceof WC_YabandPayUnionPayWap)
                && (!$gateway instanceof WC_YabandPayUnionPayCard)
                && (!$gateway instanceof WC_YabandPayUnionPayQuickPass)) {
                continue;
            }
            $gateway->update_option($gateway->getEnableKey(), $enable);
        }
    }

    protected function updateYabandPayOptions($account = null, $token = null, $desc = null, $exchange_rate = null)
    {
        /**
         * @var WC_Payment_Gateway $gateway
         */
        foreach(WC()->payment_gateways()->payment_gateways() ?: array() as $gateway){
            if ((!$gateway instanceof WC_YabandPayWechatPay)
                && (!$gateway instanceof WC_YabandPayAliPay)
                && (!$gateway instanceof WC_YabandPayiDeal)
                && (!$gateway instanceof WC_YabandPayKlarna)
                && (!$gateway instanceof WC_YabandPayBancontact)
                && (!$gateway instanceof WC_YabandPayVisa)
                && (!$gateway instanceof WC_YabandPayMastercard)
                && (!$gateway instanceof WC_YabandPayPayPal)
                && (!$gateway instanceof WC_YabandPayUnionPay)
                && (!$gateway instanceof WC_YabandPayUnionPayWap)
                && (!$gateway instanceof WC_YabandPayUnionPayCard)
                && (!$gateway instanceof WC_YabandPayUnionPayQuickPass)) {
               continue;
            }
            if(isset($account)){
                $gateway->update_option($gateway->getAccountKey(), $account);
            }
            if(isset($token)){
                $gateway->update_option($gateway->getTokenKey(), $token);
            }
            if(isset($exchange_rate)){
                $gateway->update_option($gateway->getExchangeRateKey(), $exchange_rate);
            }
        }
    }

    protected static function isCheckAccountAction()
    {
        return isset($_POST['yabandpay_check_account_action']) && $_POST['yabandpay_check_account_action'] === true;
    }

    protected static function setCheckAccountAction()
    {
        $_POST['yabandpay_check_account_action'] = true;
    }

    protected static function addErrorNotice($message)
    {
        WC_Admin_Settings::add_error($message);
    }

    public function can_refund_order($order)
    {
        return false;
    }

    public function check_yabandpay_response()
    {
        
        try{
            $notify = new Notify(new Cryptography($this->getYabandpayToken()));
            $pay_info = $notify->getOrderInfo();
            $this->log(__FUNCTION__ . ':Notify info:' . var_export($pay_info, true));
            
            if(!isset($pay_info['order_id']) || empty($pay_info['order_id'])){
                Response::ajaxReturnFailure();
            }
            $order = new WC_Order($pay_info['order_id']);
            
            if(empty($order) || !$order instanceof WC_Order){
                Response::ajaxReturnFailure();
            }
            if(isset($pay_info['state']) && !empty($pay_info['state'])){
                $notify_state = $pay_info['state'];
            }else{
                Response::ajaxReturnFailure();
            }
          
            if(!\in_array($notify_state, array( self::PAY_PAID, self::PAY_REFUNDED ))){
                Response::ajaxReturnSuccess();
            }
            $order_status = $order->get_status();
            
            if(($order_status === self::PAY_PENDING) && $notify_state === self::PAY_PAID){
                $this->orderPaymentSuccess($order,$pay_info['order_id'], $pay_info['transaction_id']);
            }elseif(($order_status === self::PAY_PAID && $order_status === self::PAY_PROCESSING) && $notify_state === self::PAY_REFUNDED){
                // @TODO 退款操作
            }
            Response::ajaxReturnSuccess();
        }catch(\Exception $e){
            $this->log(__FUNCTION__ . ':exception:' . $e->getMessage());
            Response::ajaxReturnFailure();
        }
    }

    protected function isOptionMetaKey($key)
    {
        return \strpos($key, $this->getOptionPrefixKey()) === 0;
    }

    protected function getOptionMetaValue($metakey)
    {
        foreach($_POST ?: [] as $key => $value){
            if($this->isOptionMetaKey($key) && \strpos($key, $metakey) !== false){
                return $value;
            }
        }
        return null;
    }

    public function process_payment($order_id)
    {
        $order = new WC_Order($order_id);
        if(!$order || (method_exists($order, 'is_paid') ? $order->is_paid() : \in_array($order->get_status(), [ self::PAY_PROCESSING, self::PAY_COMPLETED, self::PAY_REFUNDED ]))){
            $data = [
                'result' => 'success',
                'redirect' => $this->get_return_url($order)
            ];
            return $data;
        }
        $data = [
            'result' => 'success',
            'redirect' => $order->get_checkout_payment_url(true)
        ];
        return $data;
    }

    public function receipt_page($order_id)
    {
        try{
            $order = \wc_get_order($order_id);

            $order_number = $order->get_order_number();

            $product_list = array_map(function($product){

                if(is_array($product) && isset($product['name'])){

                    return $product['name'];

                }elseif($product instanceof WC_Order_Item_Product){

                    return $product->get_name() . '*' . $product->get_quantity();

                }

                return null;

            }, $order->get_items()) ?: [];

            $order_description = \get_bloginfo('title') . "(" . \implode(',', $product_list) . ")";

            $cryptography = new Cryptography($this->getYabandpayToken());

            $request = new Request($cryptography);

            $api = new Api(
                new Account(
                    $this->getYabandpayAccount(), 
                    $this->getYabandpayToken()
                ), 
                $request
            );

            $total_amount = $order->get_total();

            $current_currency = $this->getCurrentCurrency();

            $fee = $this->getYabandpayFee();

            if($fee > 0){

                $total_amount = Order::calculateAmount($total_amount, str_replace('%', '', $fee));

            }

            $notify_url = $this->getNotifyUrl();

            $this->log('notify_url:' . $notify_url);

            $pay_url = $api->payment(
                $this->payment_method, 
                $order_id, 
                $total_amount, 
                $current_currency, 
                $order_number.'-'.$order_description, 
                $this->get_return_url($order), 
                $notify_url,
                $order->get_billing_email(),
                'woocomerce-yabandpay2.31.1'
            );

            \wp_redirect($pay_url);
            /*exit;
            if(Helper::isWeixin() || Helper::isAlipay()){
                \wp_redirect($pay_url);
                exit;
            }
            */?><!--
            <div id="pay_qrcode"></div>
            <script type="text/javascript"
                    src="<?php /*echo \plugins_url(\basename(__DIR__) . '/js/qrcode.js'); */?>"></script>
            <script>
                jQuery(function ($) {
                    new QRCode(document.getElementById("pay_qrcode"), {
                        text: "<?php /*echo $pay_url; */?>",
                        width: 240,
                        height: 240,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.H
                    });
                    var times = 0;
                    var interval = setInterval(function () {
                        if (times >= 200) {
                            clearInterval(interval);
                        }
                        times++;
                        $.post('<?php /*echo Api::orderQueryUrl();*/?>', '<?php /*echo $request->completion([ 'trade_id' => $trade_id, 'user' => $this->getYabandpayAccount() ]);*/?>', function (res) {
                            if (res.result === '<?php /*echo Request::SUCCESS;*/?>' && res.data.state === '<?php /*echo Payment::PAY_PAID;*/?>') {
                                clearInterval(interval);
                                window.location.href = '<?php /*echo $this->get_return_url($order);*/?>&trade_id=<?php /*echo $trade_id;*/?>';
                                return false;
                            }
                        }, 'json');
                    }, 2000);
                });
            </script>-->
            <?php
        }catch(\Exception $e){
            // wc_add_notice(__('Server error, please try again .', $this->domain), 'error');
            // wc_add_notice(__($e->getMessage()), 'error');
            $this->log(__FUNCTION__ . ':' . $e->getMessage());

            $error_hint_url = 'https://partner.yabandpay.com/payments/error?message='.\urlencode($e->getMessage());

            \wp_redirect($error_hint_url);
            // die;
        }
    }

    public function process_refund($order_id, $amount = null, $reason = '')
    {
        return new WP_Error('error', __('Temporarily unavailable.', $this->domain));
    }

    private function orderPaymentSuccess(WC_Order $order, $order_id, $transaction_id)
    {
        $this->saveOrderMetaValue($order_id, 'wechatpay_transaction_id', $transaction_id);
        $order->payment_complete($transaction_id);
    }

    /**
     * @return mixed
     */
    public function getOptionPrefixKey()
    {
        return $this->option_prefix_key ?: 'woocommerce_' . $this->id . '_';
    }

    /**
     * @param mixed $option_prefix_key
     */
    public function setOptionPrefixKey()
    {
        $this->option_prefix_key = 'woocommerce_' . $this->id . '_';
    }

    /**
     * @return string
     */
    public function getMethodDescription()
    {
        return $this->method_description;
    }

    /**
     * @param string $method_description
     */
    public function setMethodDescription()
    {
        //$this->method_description = __($this->get_option('payment_desc'), $this->domain);
        $this->method_description = __($this->get_option('description'), $this->domain);
    }


    /**
     * @return mixed
     */
    public function getEnableKey($prefix = false)
    {
        if($prefix === false){
            return $this->enable_key;
        }else{
            return $this->id . '_' . $this->enable_key;
        }
    }

    public function getAccountKey()
    {
        return 'yabandpay_account';
    }

    public function getTokenKey()
    {
        return 'yabandpay_token';
    }

    public function getExchangeRateKey()
    {
        return 'exchange_rate';
    }

    public function getFeeKey()
    {
        return 'yabandpay_fee';
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Cryptography
     */
    public function getCryptography()
    {
        return $this->cryptography;
    }

    /**
     * @param Cryptography $cryptography
     */
    public function setCryptography(Cryptography $cryptography)
    {
        $this->cryptography = $cryptography;
    }

    /**
     * @return mixed
     */
    public function getPaymentMethod()
    {
        return $this->payment_method;
    }

    /**
     * @param mixed $payment_method
     */
    public function setPaymentMethod($payment_method)
    {
        $this->payment_method = $payment_method;
    }

    /**
     * @return mixed
     */
    public function getExchangeRate()
    {
        return $this->exchange_rate;
    }

    /**
     * @param mixed $exchange_rate
     */
    public function setExchangeRate()
    {
        $this->exchange_rate = $this->get_option('exchange_rate');
    }

    public function getYabandpayFee()
    {
        return floatval($this->get_option('yabandpay_fee'));
    }

    /**
     * @return mixed
     */
    public function getOrderButtonText()
    {
        return $this->order_button_text;
    }

    /**
     * @param mixed $order_button_text
     */
    public function setOrderButtonText()
    {
        $this->order_button_text = __($this->title, $this->domain);
    }

    public function init_form_fields()
    {
        $this->form_fields['enabled'] = [
            'title' => __('Enable/Disable', $this->domain),
            'type' => 'checkbox',
            'label' => __('Enable YabandPay - ' . $this->payment_method, $this->domain),
            'default' => 'no'
        ];
        $this->form_fields['Title'] = [
            'title' => __('Title',$this->domain),
            'type' => 'text',
            'description' => 'This controls the title which the user sees during checkout.',
            'css' => 'width:500px',
            'default' => $this->title,
            'desc_tip' => true
        ];
        $this->form_fields['yabandpay_account'] = [
            'title' => __('Account', $this->domain),
            'type' => 'text',
            'description' => __('Please enter the email address of Cashier', $this->domain),
            'placeholder' => __('Please enter the email address of Cashier', $this->domain),
            'css' => 'width:500px',
            'custom_attributes' => array(
                'required' => true,
                'oninvalid' => 'setCustomValidity("' . __('Please enter the email address of Cashier', $this->domain) . '");',
                'oninput' => 'setCustomValidity("");'
            ),
            'desc_tip' => true,
        ];
        $this->form_fields['yabandpay_token'] = [
            'title' => __('Token', $this->domain),
            'type' => 'password',
            'description' => __('Please ask for your token from YabandPay', $this->domain),
            'css' => 'width:500px',
            'placeholder' => __('Please ask for your token from YabandPay', $this->domain),
            'custom_attributes' => array(
                'required' => true,
                'oninvalid' => 'setCustomValidity("' . __('Please ask for your token from YabandPay', $this->domain) . '");',
                'oninput' => 'setCustomValidity("");'
            ),
            'desc_tip' => true,
        ];
        $this->form_fields['yabandpay_fee'] = [
            'title' => __('Fee', $this->domain),
            'type' => 'text',
            'description' => '整数/小数 (%)',
            'css' => 'width:500px',
            'placeholder' => '',
            'desc_tip' => false,
            'default' => ''
        ];
        $this->form_fields['description'] = [
            'title' => __('Description',$this->domain),
            'type' => 'text',
            'description' => 'This  description controls the title which the user sees during checkout.',
            'css' => 'width:500px',
            'desc_tip' => true,
            'default' => $this->description
        ];
        $this->form_fields['yabandpay_debug'] = [
            'title' => __('Debug Log', $this->domain),
            'type' => 'checkbox',
            'label' => __("Log payment information. Logfiles can be found at: wp-content/plugins/yabandpay-for-woocommerce/log"),
            'default' => 'no'
        ];
    }

    protected function cancelPayment($order)
    {
        $order_id = $this->getOrderId($order);
        $trade_id = $this->getOrderMetaValue($order_id, 'ya_order_id');
        if(empty($trade_id)){
            return false;
        }
        try{
            $cryptography = new Cryptography($this->getYabandpayToken());
            $request = new Request($cryptography);
            $api = new Api(new Account($this->getYabandpayAccount(), $this->getYabandpayToken()), $request);
            $api->cancel($trade_id);
            return true;
        }catch(\Exception $e){
            $this->log(__FUNCTION__ . ':exception:' . $e->getMessage());
            return false;
        }
    }

    protected function refuned($order, $description)
    {
        $order_id = $this->getOrderId($order);
        $trade_id = $this->getOrderMetaValue($order_id, 'ya_order_id');
        if(empty($trade_id)){
            return false;
        }
        try{
            $cryptography = new Cryptography($this->getYabandpayToken());
            $request = new Request($cryptography);
            $api = new Api(new Account($this->getYabandpayAccount(), $this->getYabandpayToken()), $request);
            $api->refund($trade_id, $description);
            return true;
        }catch(\Exception $e){
            $this->log(__FUNCTION__ . ':exception:' . $e->getMessage());
            return false;
        }
    }

    protected static function isLang($lang)
    {
        $current_locale = is_admin() ? get_user_locale() : get_locale();
        return ($current_locale == $lang) ? true : false;
    }

    public function getYabandpayAccount()
    {
        return $this->yabandpay_account ?: $this->get_option('yabandpay_account');
    }

    public function setYabandpayAccount()
    {
        if(empty($this->yabandpay_account)){
            $this->yabandpay_account = $this->get_option('yabandpay_account');
        }
    }

    public function getYabandpayToken()
    {
        return $this->yabandpay_token ?: $this->get_option('yabandpay_token');
    }

    public function setYabandpayToken()
    {
        if(empty($this->yabandpay_token)){
            $this->yabandpay_token = $this->get_option('yabandpay_token');
        }
    }

    /**
     * @return array
     */
    public function getSupportedCurrencies()
    {
        return $this->supported_currencies ?: array( Currency::CNY, Currency::EUR );
    }

    /**
     * @param array $supported_currencies
     */
    public function setSupportedCurrencies(array $supported_currencies = array( Currency::CNY, Currency::EUR ))
    {
        $this->supported_currencies = $supported_currencies;
    }

    protected function getOrderId($order)
    {
        $order = \wc_get_order($order);
        if($order instanceof WC_Order){
            return $order->get_id();
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getMethodTitle()
    {
        return $this->method_title ?: __($this->pay_text, $this->domain);
    }

    /**
     * @param mixed $method_title
     */
    public function setMethodTitle()
    {
        $this->method_title = $this->pay_text;
    }

    /**
     * @return mixed
     */
    public function getNotifyUrl()
    {
        return $this->notify_url ?: WC()->api_request_url($this->notify_url);
    }

    /**
     * @param mixed $notify_url
     */
    public function setNotifyUrl()
    {
        $this->notify_url = WC()->api_request_url($this->notify_url);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        //return $this->title ?: $this->get_option($this->id . '_title');
        return $this->title ?: $this->get_option($this->id . 'Title');
    }

    /**
     * @param string $title
     */
    public function setTitle()
    {
        $this->title = __($this->title, $this->domain);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        //return $this->description;
        return $this->description ?: $this->get_option($this->id . 'description');
    }

    /**
     * @param string $description
     */
    public function setDescription()
    {
        //$this->description = __($this->method_description, $this->domain);
        $this->description = __($this->description, $this->domain);
    }

    public function getYabandpayDebug()
    {
        return $this->yabandpay_debug ?: $this->get_option('yabandpay_debug');
    }

    public function setYabandpayDebug()
    {
        if(empty($this->yabandpay_debug)){
            $this->yabandpay_debug = $this->get_option('yabandpay_debug');
        }
    }

    private function getDefaultDesc(){
        if($this->payment_method === Payment::ALIPAY){
            $desc = '超优惠率，笔笔省';
        }elseif($this->payment_method === Payment::IDEAL){
            $desc = 'iDEAL支付，快捷便利';
        }elseif($this->payment_method === Payment::SOFORT){
            $desc = '欢迎使用Sofort';
        }elseif($this->payment_method === Payment::BANCONTACT){
            $desc = '欢迎使用Bancontact';
        }elseif($this->payment_method === Payment::VISA){
            $desc = '欢迎使用Visa';
        }elseif($this->payment_method === Payment::MASTERCARD){
            $desc = '欢迎使用Mastercard';
        }elseif($this->payment_method === Payment::PAYPAL){
            $desc = '欢迎使用PayPal';
        }elseif($this->payment_method === Payment::UNION){
            $desc = '银联支付，快捷便利';
        }else{
            $desc = '汇率低，优惠多';
        }

        return $desc;
    }

    /**
     * @return mixed
     */
    public function getCurrentCurrency()
    {
        return $this->current_currency;
    }

    /**
     * @param mixed $current_currency
     */
    public function setCurrentCurrency()
    {
        $this->current_currency = \get_woocommerce_currency();
    }

    public function saveOrderMetaValue($order_id, $meta_key, $meta_value)
    {
        $value = \get_post_meta($order_id, $meta_key, true);
        if($value === false){
            \add_post_meta($order_id, $meta_key, $meta_value);
        }else{
            \update_post_meta($order_id, $meta_key, $meta_value);
        }
    }

    public function getOrderMetaValue($order_id, $meta_key)
    {
        $value = \get_post_meta($order_id, $meta_key, true);
        return $value;
    }

    protected function log($message)
    {
        
        if($this->getYabandpayDebug() === 'yes'){
            $dirname = __DIR__ . '/log';
            if(!is_dir($dirname)){
                @mkdir($dirname, 0775, true);
            }
            return \file_put_contents($dirname . '/debug' . date('Ymd') . '.log', date('Y-m-d H:i:s') . '|' . urldecode($message) . PHP_EOL, FILE_APPEND);
        }
    }
}