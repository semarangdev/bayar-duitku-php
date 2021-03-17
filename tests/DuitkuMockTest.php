<?php

namespace SemarangDev\Duitku\Tests;

use PHPUnit\Framework\TestCase;
use SemarangDev\Duitku\Duitku;
use SemarangDev\Duitku\DuitkuMock;
use SemarangDev\Duitku\Model\Billing;
use SemarangDev\Duitku\Model\Callback;
use SemarangDev\Duitku\Model\Config;
use SemarangDev\Duitku\Model\Item;

class DuitkuTest extends TestCase
{
    /** @test */
    public function successfully_request_a_transaction()
    {
        $items = [
            Item::make([
                'name' => 'Laptop Bagus',
                'price' => 10000000,
                'quantity' => 1,
            ]),
        ];
        $billing = Billing::make([
            'order_id' => 'ORD.2020-01-01.X01',
            'customer' => 'Wiratama Pratama',
            'email' => 'wiratama@pratama.com',
            'phone' => '+6281782777434',
            'items' => $items,
            'payment_method' => 'BT',
        ]);

        // without mock
        // $config = Config::make([
        //     'merchant_code' => 'DUITKU_MERCHANT_CODE',
        //     'merchant_key' => 'DUIT',
        //     'base_url' => 'https://sandbox.duitku.com',
        //     'callback_url' => '',
        //     'redirect_url' => '',
        // ]);
        // $duitku = Duitku::init($config);
        // with mock
        $duitku = DuitkuMock::init(null);
        $result = $duitku->pay($billing);

        $this->assertArrayHasKey('request', $result);
        $this->assertArrayHasKey('response', $result);
        $this->assertArrayHasKey('paymentUrl', $result['response']);
        $this->assertArrayHasKey('callback_signature', $result);

        $this->assertSame($billing->order_id, $result['request']['merchantOrderId']);
        $this->assertSame($billing->payment_method, $result['request']['paymentMethod']);
        $this->assertSame($billing->total, $result['request']['paymentAmount']);

        // echo json_encode($result);
    }

    /** @test */
    public function successfully_check_a_transaction()
    {
        // without mock
        // $config = Config::make([
        //     'merchant_code' => 'DUITKU_MERCHANT_CODE',
        //     'merchant_key' => 'DUIT',
        //     'base_url' => 'https://sandbox.duitku.com',
        //     'callback_url' => '',
        //     'redirect_url' => '',
        // ]);
        // $duitku = Duitku::init($config);
        // with mock
        $duitku = DuitkuMock::init(null);

        $orderId = 'ORD.2020-01-01.X01';

        $result = $duitku->check($orderId);

        $this->assertArrayHasKey('request', $result);
        $this->assertArrayHasKey('response', $result);

        $this->assertSame($orderId, $result['request']['merchantOrderId']);
        $this->assertSame($orderId, $result['response']['merchantOrderId']);
        $this->assertSame('00', $result['response']['statusCode']);
        $this->assertSame('SUCCESS', $result['response']['statusMessage']);

        // echo json_encode($result);
    }

    /** @test */
    public function successfully_handle_a_callback()
    {
        $duitku = DuitkuMock::init(null);

        $data = $duitku->requestCallbackSuccess([
            'merchantOrderId' => 'ORD.2020-01-01.X12',
            'amount' => 20000,
            'signature' => md5('callback'),
        ]); // $_POST

        $callback = Callback::make($data);

        $signatureVerified = $callback->isSignatureVerified(md5('callback'));
        $isSuccess = $callback->isSuccess();

        $this->assertSame(true, $signatureVerified);
        $this->assertSame(true, $isSuccess);

        // echo json_encode($callback->toArray());
    }
}
