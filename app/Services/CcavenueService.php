<?php

namespace App\Services;

use App\Models\Order;
use Exception;

class CcavenueService
{
    protected string $merchantId;
    protected string $accessCode;
    protected string $workingKey;
    protected string $mode;
    protected string $iv;

    public function __construct()
    {
        $this->merchantId = (string) (setting('ccavenue_merchant_id') ?: config('ccavenue.merchant_id', ''));
        $this->accessCode = (string) (setting('ccavenue_access_code') ?: config('ccavenue.access_code', ''));
        $this->workingKey = (string) (setting('ccavenue_working_key') ?: config('ccavenue.working_key', ''));
        $this->mode       = (string) (setting('ccavenue_mode') ?: config('ccavenue.mode', 'test'));
        
        // CCAvenue standard 16-byte fixed IV
        $this->iv = pack('C*', 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
    }

    public function isConfigured(): bool
    {
        return !empty($this->merchantId) && !empty($this->accessCode) && !empty($this->workingKey);
    }

    public function getActionUrl(): string
    {
        if ($this->mode === 'mock') {
            return route('ccavenue.mock');
        }

        $endpoints = config('ccavenue.endpoints', []);
        return $this->mode === 'live'
            ? ($endpoints['live'] ?? 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction')
            : ($endpoints['test'] ?? 'https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction');
    }

    public function getAccessCode(): string
    {
        return $this->accessCode;
    }

    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    /**
     * CCAvenue AES-128-CBC encryption.
     */
    public function encrypt(string $plainText, ?string $key = null): string
    {
        $workingKey = $key ?: $this->workingKey;
        $secretKey = md5($workingKey, true);
        $encrypted = openssl_encrypt($plainText, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $this->iv);
        return bin2hex($encrypted);
    }

    /**
     * CCAvenue AES-128-CBC decryption.
     */
    public function decrypt(string $cipherTextHex, ?string $key = null): string
    {
        $workingKey = $key ?: $this->workingKey;
        $secretKey = md5($workingKey, true);
        $binary = @hex2bin(trim($cipherTextHex));
        if ($binary === false) {
            return '';
        }
        $decrypted = openssl_decrypt($binary, 'AES-128-CBC', $secretKey, OPENSSL_RAW_DATA, $this->iv);
        return $decrypted !== false ? $decrypted : '';
    }

    /**
     * Build payment request parameters for a given Order.
     */
    public function buildRequestParams(Order $order): array
    {
        $redirectUrl = route('ccavenue.response');
        $cancelUrl   = route('ccavenue.cancel');

        $billingName = $order->ship_name ?: ($order->customer->full_name ?? 'Valued Patron');
        $billingAddress = trim(($order->ship_line1 ?? '') . ' ' . ($order->ship_line2 ?? ''));
        $billingCity = $order->ship_city ?: 'Kolkata';
        $billingState = $order->ship_state ?: 'West Bengal';
        $billingZip = $order->ship_postcode ?: '700016';
        $billingCountry = $order->ship_country ?: 'India';
        $billingTel = $order->phone ?: ($order->customer->phone ?? '9999999999');
        $billingEmail = $order->email ?: ($order->customer->email ?? 'guest@toramally.com');

        $params = [
            'merchant_id'      => $this->merchantId,
            'order_id'         => $order->order_no,
            'amount'           => number_format($order->total_inr, 2, '.', ''),
            'currency'         => $order->currency ?: 'INR',
            'redirect_url'     => $redirectUrl,
            'cancel_url'       => $cancelUrl,
            'language'         => 'EN',
            'billing_name'     => $billingName,
            'billing_address'  => $billingAddress,
            'billing_city'     => $billingCity,
            'billing_state'    => $billingState,
            'billing_zip'      => $billingZip,
            'billing_country'  => $billingCountry,
            'billing_tel'      => $billingTel,
            'billing_email'    => $billingEmail,
            'delivery_name'    => $billingName,
            'delivery_address' => $billingAddress,
            'delivery_city'    => $billingCity,
            'delivery_state'   => $billingState,
            'delivery_zip'     => $billingZip,
            'delivery_country' => $billingCountry,
            'delivery_tel'     => $billingTel,
            'merchant_param1'  => (string) $order->id,
            'merchant_param2'  => (string) ($order->customer_id ?? ''),
            'merchant_param3'  => 'web_order',
            'merchant_param4'  => $order->access_token,
            'promo_code'       => '',
            'customer_identifier' => (string) ($order->customer_id ?? ''),
        ];

        return $params;
    }

    /**
     * Get the complete payload ready to submit to CCAvenue gateway form.
     */
    public function getPaymentPayload(Order $order): array
    {
        $params = $this->buildRequestParams($order);

        $merchantData = '';
        foreach ($params as $key => $val) {
            $merchantData .= $key . '=' . urlencode((string) $val) . '&';
        }
        $merchantData = rtrim($merchantData, '&');

        $encRequest = $this->encrypt($merchantData);

        return [
            'action_url'  => $this->getActionUrl(),
            'access_code' => $this->accessCode,
            'encRequest'  => $encRequest,
            'order'       => $order,
        ];
    }

    /**
     * Parse and decrypt CCAvenue response.
     */
    public function parseResponse(string $encResp): array
    {
        $decrypted = $this->decrypt($encResp);
        if (empty($decrypted)) {
            return [];
        }

        $data = [];
        parse_str($decrypted, $data);

        // Sanitize string values
        $clean = [];
        foreach ($data as $k => $v) {
            $clean[$k] = is_string($v) ? trim($v) : $v;
        }

        return $clean;
    }
}
