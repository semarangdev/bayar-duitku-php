<?php

namespace SemarangDev\Duitku\Model;

/**
 * Map duitku callback request to a model.
 * @see https://docs.duitku.com/docs-api.html#callback
 */
class Callback extends Model
{
    public static $initiates = [
        'merchantCode',
        'amount',
        'merchantOrderId',
        'productDetail',
        'additionalParam',
        'paymentCode',
        'resultCode',
        'merchantUserId',
        'reference',
        'signature',
    ];

    public function verifySignature(string $callbackSignature): bool
    {
        return $this->signature == $callbackSignature;
    }

    public function isSignatureVerified(string $callbackSignature): bool
    {
        return $this->verifySignature($callbackSignature);
    }

    public function isSuccess()
    {
        return $this->resultCode == '00';
    }
}
