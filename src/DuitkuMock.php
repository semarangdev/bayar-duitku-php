<?php

namespace SemarangDev\Duitku;

use SemarangDev\Duitku\Model\Config;

class DuitkuMock extends Duitku
{
    public function __construct()
    {
        $config = Config::make([
            'merchant_code' => 'MOCK1',
            'merchant_key' => 'mock1111111111111111111111111111',
            'base_url' => 'https://sandbox.duitku.com',
            'callback_url' => 'https://myapp.test/payment/duitku/callback',
            'redirect_url' => 'https://myapp.test/payment/duitku/redirect',
        ]);

        parent::__construct($config);
    }

    public static function init($config = null): self
    {
        return new static();
    }

    public function callApi(string $path, array $data): array
    {
        if ($path == static::PATH_NEW_TRANSACTION) {
            return $this->responseNewTransactionSuccess($data);
        }

        if ($path == static::PATH_CHECK_TRANSACTION) {
            return $this->responseCheckTransactionSuccess($data);
        }

        return [];
    }

    /**
     * Example response from duitku when new transaction success.
     * @see https://docs.duitku.com/docs-api.html#request-transaksi
     */
    public function responseNewTransactionSuccess(array $data): array
    {
        $ref = bin2hex(openssl_random_pseudo_bytes(5));

        return [
            'paymentUrl' => "https://sandbox.duitku.com/topup/topupdirectv2.aspx?ref={$ref}",
            'merchantCode' => $data['merchantCode'],
            'reference' => "MOCK{$ref}",
            'vaNumber' => '86800110052' . rand(11111, 99999),
            'amount' => $data['paymentAmount'],
            'statusCode' => '00',
            'statusMessage' => 'SUCCESS',
        ];
    }

    /**
     * Example response from duitku when new transaction failed.
     * @see https://docs.duitku.com/docs-api.html#request-transaksi
     */
    public function responseNewTransactionFailed(): array
    {
        return [
            'message' => 'Error has ben occured.',
        ];
    }

    /**
     * Example response from duitku when check transaction success.
     * @see https://docs.duitku.com/docs-api.html#check-transaction
     */
    public function responseCheckTransactionSuccess(array $data): array
    {
        $ref = bin2hex(openssl_random_pseudo_bytes(5));

        return [
            'merchantOrderId' => $data['merchantOrderId'],
            'reference' => "MOCK{$ref}",
            'amount' => $data['amount'] ?? 100000,
            'statusCode' => '00',
            'statusMessage' => 'SUCCESS',
        ];
    }

    /**
     * Example request from duitku when callback occured.
     * @see https://docs.duitku.com/docs-api.html#request-transaksi
     */
    public function requestCallbackSuccess(array $data): array
    {
        $ref = bin2hex(openssl_random_pseudo_bytes(5));

        return [
            'merchantCode' => $this->config->merchant_code,
            'amount' => $data['amount'] ?? 100000,
            'merchantOrderId' => $data['orderId'] ?? 'ORD.2020-01-01.XX1',
            'productDetail' => $data['productDetail'] ?? 'Detail Produk',
            'additionalParam' => $data['additionalParam'] ?? [],
            'paymentCode' => $data['paymentCode'] ?? 'BT', // permatabank
            'resultCode' => $data['resultCode'] ?? '00', // success
            'merchantUserId' => $data['merchantUserId'] ?? 'email@user.example',
            'reference' => $data['reference'] ?? "MOCK{$ref}",
            'signature' => $data['signature'] ?? md5(microtime()),
        ];
    }
}
