<?php

namespace SemarangDev\Duitku;

use SemarangDev\Duitku\Exception\DuitkuException;
use SemarangDev\Duitku\Model\Billing;
use SemarangDev\Duitku\Model\Config;

class Duitku
{
    /** @see https://docs.duitku.com/docs-api.html#payment-method */
    public const PAYMENT_METHODS = [
        'VC' => 'Credit Card (Visa / Master)',
        'BK' => 'BCA KlikPay',
        'M1' => 'Mandiri Virtual Account',
        'BT' => 'Permata Bank Virtual Account',
        'B1' => 'CIMB Niaga Virtual Account',
        'A1' => 'ATM Bersama',
        'I1' => 'BNI Virtual Account',
        'VA' => 'Maybank Virtual Account',
        'FT' => 'Ritel',
        'OV' => 'OVO',
    ];

    /** @see https://docs.duitku.com/docs-api.html#callback9 */
    public const CALLBACK_RESPONSES = [
        '00' => 'Success',
        '01' => 'Failed',
    ];

    /** @see https://docs.duitku.com/docs-api.html#redirect10 */
    public const REDIRECT_RESPONSES = [
        '00' => 'Success',
        '01' => 'Pending',
        '02' => 'Canceled',
    ];

    public const PATH_NEW_TRANSACTION = '/webapi/api/merchant/v2/inquiry';
    public const PATH_CHECK_TRANSACTION = '/webapi/api/merchant/transactionStatus';

    public $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public static function init(Config $config): self
    {
        return new static($config);
    }

    public function pay(Billing $billing)
    {
        return $this->newTransaction($billing);
    }

    public function check(string $orderId)
    {
        return $this->checkTransaction($orderId);
    }

    /**
     * @see https://docs.duitku.com/docs-api.html#request-transaksi
     */
    public function newTransaction(Billing $billing)
    {
        // format: MD5(merchantcode + merchantOrderId + amount + merchantKey)
        $requestSignature = md5(
            $this->config->merchant_code . $billing->order_id
                . $billing->total . $this->config->merchant_key
        );

        // for later use to verify callback signature
        // format:  MD5(merchantcode + amount + merchantOrderId + merchantKey)
        $callbackSignature = md5(
            $this->config->merchant_code . $billing->total
                . $billing->order_id . $this->config->merchant_key
        );

        $data = [
            'merchantCode' => $this->config->merchant_code,
            'paymentAmount' => $billing->total,
            'paymentMethod' => $billing->payment_method,
            'merchantOrderId' => $billing->order_id,
            'productDetails' => 'Pembayaran Duitku', // TODO: configurable
            'additionalParam' => json_encode([]), // TODO: configurable
            'merchantUserInfo' => $billing->customer,
            'email' => $billing->email,
            'customerVaName' => "Duitku - {$billing->customer}",
            'phoneNumber' => $billing->phone,
            'itemDetails' => $billing->toArray()['items'],
            'callbackUrl' => $this->config->callback_url,
            'returnUrl' => $this->config->redirect_url,
            'signature' => $requestSignature,
            'expiryPeriod' => '120', // 2 jam TODO: configurable
        ];

        $response = $this->callApi(static::PATH_NEW_TRANSACTION, $data);

        if (! array_key_exists('paymentUrl', $response)) {
            throw new DuitkuException(
                'Response paymentUrl not exists. '
                    . 'Actual response: ' . json_encode($response)
            );
        }

        return [
            'request' => $data,
            'response' => $response,
            'callback_signature' => $callbackSignature,
        ];
    }

    /**
     * @see https://docs.duitku.com/docs-api.html#check-transaction
     */
    public function checkTransaction(string $orderId)
    {
        // format: md5(merchantCode + merchantOrderId + merchantKey)
        $signature = md5($this->config->merchant_code . $orderId . $this->config->merchant_key);

        $data = [
            'merchantCode' => $this->config->merchant_code,
            'merchantOrderId' => $orderId,
            'signature' => $signature,
        ];

        $response = $this->callApi(static::PATH_CHECK_TRANSACTION, $data);

        return [
            'request' => $data,
            'response' => $response,
        ];
    }

    public function callApi(string $path, array $data): array
    {
        $url = $this->config->base_url . $path;
        $headers = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($data)),
        ];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        try {
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        } catch (\Exception $e) {
            throw new DuitkuException('Request to duitku api failed: ' . $e->getMessage());
        }

        if ($httpCode != 200) {
            throw new DuitkuException('Request to duitku api failed. Response: ' . $response);
        }

        return json_decode((string) $response, true);
    }
}
