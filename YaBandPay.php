<?php
/*
* Plugin Name: YabandPay For WooCommerce
* Plugin URI: https://wordpress.org/plugins/yabandpay-for-woocommerce/
* Description: YabandPay B.V. is a regulated Dutch payment institution that holds a payment license issued by the De Nederlandsche Bank (DNB). Our relationship number: R188633; Chamber of Commerce number: 85678988.
* Version: 4.5.7
* Author: YabandPay
* Author URI: https://www.yabandpay.com/
*
* Text Domain: yabandpay
* Domain Path: /lang/
* WC tested up to: 6.4.3
*/

if(!defined('ABSPATH')) exit; // Exit if accessed directly


add_action('plugins_loaded', function(){
    if(!class_exists('WC_Payment_Gateway')){
        return;
    }
    include_once(__DIR__ . '/vendor/autoload.php');
    include_once(__DIR__ . '/class-wc-yabandpay.php');
    include_once(__DIR__ . '/class-wc-yabandpaywechatpay.php');
    include_once(__DIR__ . '/class-wc-yabandpayalipay.php');
    // include_once(__DIR__ . '/class-wc-yabandpayunionpay.php');
    include_once(__DIR__ . '/class-wc-yabandpayunionpaywap.php');
    // include_once(__DIR__ . '/class-wc-yabandpayunionpaycard.php');
    // include_once(__DIR__ . '/class-wc-yabandpayunionpayquickpass.php');
    include_once(__DIR__ . '/class-wc-yabandpayideal.php');
    include_once(__DIR__ . '/class-wc-yabandpayklarna.php');
    include_once(__DIR__ . '/class-wc-yabandpaybancontact.php');
    include_once(__DIR__ . '/class-wc-yabandpayvisa.php');
    include_once(__DIR__ . '/class-wc-yabandpaymastercard.php');
    include_once(__DIR__ . '/class-wc-yabandpaypaypal.php');

    // 语言翻译
    \load_plugin_textdomain('yabandpay', false, dirname(plugin_basename(__FILE__)) . '/lang/');

    // 告诉WC存在的支付
    add_filter('woocommerce_payment_gateways', function($methods){
        $methods[] = 'WC_YabandPayWechatPay';
        $methods[] = 'WC_YabandPayAliPay';
        // $methods[] = 'WC_YabandPayUnionPay';
        $methods[] = 'WC_YabandPayUnionPayWap';
        // $methods[] = 'WC_YabandPayUnionPayCard';
        // $methods[] = 'WC_YabandPayUnionPayQuickPass';
        $methods[] = 'WC_YabandPayiDeal';
        $methods[] = 'WC_YabandPayKlarna';
        $methods[] = 'WC_YabandPayBancontact';
        $methods[] = 'WC_YabandPayVisa';
        $methods[] = 'WC_YabandPayMastercard';
        $methods[] = 'WC_YabandPayPaypal';
        return $methods;
    });

    add_filter('plugin_action_links_' . plugin_basename(__FILE__), function($links){
        return array_merge(
            [
                'settings' => '<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=wc_yabandpay') . '">' . __('Settings', 'yabandpay') . '</a>'
            ],
            $links
        );
    });

    $wechatpay = new WC_YabandPayWechatPay();
    \add_action('woocommerce_receipt_' . $wechatpay->id, [ $wechatpay, 'receipt_page' ]);
    \add_action('woocommerce_api_wc_' . $wechatpay->id, [ $wechatpay, 'check_yabandpay_response' ]);

    $alipay = new WC_YabandPayAliPay();
    \add_action('woocommerce_receipt_' . $alipay->id, [ $alipay, 'receipt_page' ]);
    \add_action('woocommerce_api_wc_' . $alipay->id, [ $alipay, 'check_yabandpay_response' ]);

    // $unionpay = new WC_YabandPayUnionPay();
    // \add_action('woocommerce_receipt_' . $unionpay->id, [ $unionpay, 'receipt_page' ]);
    // \add_action('woocommerce_api_wc_' . $unionpay->id, [ $unionpay, 'check_yabandpay_response' ]);

    $unionpaywap = new WC_YabandPayUnionPayWap();
    \add_action('woocommerce_receipt_' . $unionpaywap->id, [ $unionpaywap, 'receipt_page' ]);
    \add_action('woocommerce_api_wc_' . $unionpaywap->id, [ $unionpaywap, 'check_yabandpay_response' ]);

    // $unionpaycard = new WC_YabandPayUnionPayCard();
    // \add_action('woocommerce_receipt_' . $unionpaycard->id, [ $unionpaycard, 'receipt_page' ]);
    // \add_action('woocommerce_api_wc_' . $unionpaycard->id, [ $unionpaycard, 'check_yabandpay_response' ]);

    // $unionpayquickpass = new WC_YabandPayUnionPayQuickPass();
    // \add_action('woocommerce_receipt_' . $unionpayquickpass->id, [ $unionpayquickpass, 'receipt_page' ]);
    // \add_action('woocommerce_api_wc_' . $unionpayquickpass->id, [ $unionpayquickpass, 'check_yabandpay_response' ]);

    $ideal = new WC_YabandPayiDeal();
    \add_action('woocommerce_receipt_' . $ideal->id, [ $ideal, 'receipt_page' ]);
    \add_action('woocommerce_api_wc_' . $ideal->id, [ $ideal, 'check_yabandpay_response' ]);

    $klarna = new WC_YabandPayKlarna();
    \add_action('woocommerce_receipt_' . $klarna->id, [ $klarna, 'receipt_page' ]);
    \add_action('woocommerce_api_wc_' . $klarna->id, [ $klarna, 'check_yabandpay_response' ]);

    $bancontact = new WC_YabandPayBancontact();
    \add_action('woocommerce_receipt_' . $bancontact->id, [ $bancontact, 'receipt_page' ]);
    \add_action('woocommerce_api_wc_' . $bancontact->id, [ $bancontact, 'check_yabandpay_response' ]);

    $visa = new WC_YabandPayVisa();
    \add_action('woocommerce_receipt_' . $visa->id, [ $visa, 'receipt_page' ]);
    \add_action('woocommerce_api_wc_' . $visa->id, [ $visa, 'check_yabandpay_response' ]);

    $mastercard = new WC_YabandPayMastercard();
    \add_action('woocommerce_receipt_' . $mastercard->id, [ $mastercard, 'receipt_page' ]);
    \add_action('woocommerce_api_wc_' . $mastercard->id, [ $mastercard, 'check_yabandpay_response' ]);

    $paypal = new WC_YabandPayPayPal();
    \add_action('woocommerce_receipt_' . $paypal->id, [ $paypal, 'receipt_page' ]);
    \add_action('woocommerce_api_wc_' . $paypal->id, [ $paypal, 'check_yabandpay_response' ]);


});