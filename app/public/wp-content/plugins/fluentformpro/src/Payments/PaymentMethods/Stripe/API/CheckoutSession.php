<?php

namespace FluentFormPro\Payments\PaymentMethods\Stripe\API;

use FluentFormPro\Payments\PaymentMethods\Stripe\StripeSettings;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handle Stripe Checkout Session
 * @since 1.0.0
 */
class CheckoutSession
{
    public static function create($args)
    {
        $argsDefault = [
            'payment_method_types' => ['card'],
            'locale'               => 'auto'
        ];

        $args = wp_parse_args($args, $argsDefault);

        ApiRequest::set_secret_key(StripeSettings::getSecretKey());
        return ApiRequest::request($args, 'checkout/sessions');
    }

    public static function retrieve($sessionId, $args = [])
    {
        ApiRequest::set_secret_key(StripeSettings::getSecretKey());
        return ApiRequest::request($args, 'checkout/sessions/' . $sessionId, 'GET');
    }
}