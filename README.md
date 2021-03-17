# Duitku PHP

[![Latest Version on Packagist](https://img.shields.io/packagist/v/semarangdev/bayar-duitku-php.svg?style=flat-square)](https://packagist.org/packages/semarangdev/bayar-duitku-php)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/semarangdev/bayar-duitku-php/Tests?label=tests)](https://github.com/semarangdev/bayar-duitku-php/actions?query=workflow%3ATests+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/semarangdev/bayar-duitku-php.svg?style=flat-square)](https://packagist.org/packages/semarangdev/bayar-duitku-php)

Pustaka PHP untuk gerbang pembayaran Duitku.

## Instalasi

Kamu dapat memasang pustaka ini lewat composer:

```bash
composer require semarangdev/bayar-duitku-php
```

## Pemakaian

### Inisiasi

```php
require_once 'vendor/autoload.php';

use SemarangDev\Duitku\Duitku;
use SemarangDev\Duitku\Model\Config;

$config = Config::make([
    'merchant_code' => 'MOCK1',
    'merchant_key' => 'mock1111111111111111111111111111',
    'base_url' => 'https://sandbox.duitku.com',
    'callback_url' => 'https://myapp.test/payment/duitku/callback',
    'redirect_url' => 'https://myapp.test/payment/duitku/redirect',
]);

$duitku = Duitku::init($config);
```

### Transaksi Baru

Digunakan untuk membuat transaksi baru.

```php

use SemarangDev\Duitku\Model\Item;
use SemarangDev\Duitku\Model\Billing;

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

$result = $duitku->pay($billing);
var_dump($result);
// [
//     'request' => [
//         'merchantCode' => 'MOCK1',
//         'paymentAmount' => 10000000,
//         'paymentMethod' => 'BT',
//         'merchantOrderId' => 'ORD.2020-01-01.X01',
//         'productDetails' => 'Pembayaran Duitku',
//         'additionalParam' => '[]',
//         'merchantUserInfo' => 'Wiratama Pratama',
//         'email' => 'wiratama@pratama.com',
//         'customerVaName' => 'Duitku - Wiratama Pratama',
//         'phoneNumber' => '+6281782777434',
//         'itemDetails' => [
//             [
//                 'name' => 'Laptop Bagus',
//                 'price' => 10000000,
//                 'quantity' => 1
//             ]
//         ],
//         'callbackUrl' => 'https://myapp.test/payment/duitku/callback',
//         'returnUrl' => 'https://myapp.test/payment/duitku/redirect',
//         'signature' => 'a307bba429585772779a3f14e5ded8dd',
//         'expiryPeriod' => '120'
//     ],
//     'response' => [
//         'paymentUrl' => 'https://sandbox.duitku.com/topup/topupdirectv2.aspx?ref=85dba234b8',
//         'merchantCode' => 'MOCK1',
//         'reference' => 'MOCK85dba234b8',
//         'vaNumber' => '8680011005276846',
//         'amount' => 10000000,
//         'statusCode' => '00',
//         'statusMessage' => 'SUCCESS'
//     ],
//     'callback_signature' => '469d3024acabf96281fbec5cfc2fd017'
// ]
```

#### Metode Pembayaran

Metode pembayaran yang digunakan saat membuat `Billing`.

``` php
PAYMENT_METHODS = [
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
```

### Cek Transaksi

Digunakan untuk mengecek status transaksi.

```php

$orderId = 'ORD.2020-01-01.X01';

$result = $duitku->check($orderId);
var_dump($result);
// [
//     'request' => [
//         'merchantCode' => 'MOCK1',
//         'merchantOrderId' => 'ORD.2020-01-01.X01',
//         'signature' => 'b923c874e22f3106255c4db82a80a0e3'
//     ],
//     'response' => [
//         'merchantOrderId' => 'ORD.2020-01-01.X01',
//         'reference' => 'MOCKb08bb0eeda',
//         'amount' => 100000,
//         'statusCode' => '00',
//         'statusMessage' => 'SUCCESS'
//     ]
// ]
```

### Aksi Callback

Silahkan gunakan `Callback` model untuk mengubah `request callback duitku` sehingga memprosesnya lebih mudah.

```php
use SemarangDev\Duitku\Model\Callback;

$callback = Callback::make($_POST);

var_dump($callback->toArray());
// [
//     'merchantCode' => 'MOCK1',
//     'amount' => 20000,
//     'merchantOrderId' => 'ORD.2020-01-01.XX1',
//     'productDetail' => 'Detail Produk',
//     'additionalParam' => [],
//     'paymentCode' => 'BT',
//     'resultCode' => '00',
//     'merchantUserId' => 'email@user.example',
//     'reference' => 'MOCK02857347f2',
//     'signature' => '924a8ceeac17f54d3be3f8cdf1c04eb2'
// ]

$signature = '8516b6dc29d019a9fde98220af5d233a';
$signatureVerified = $callback->isSignatureVerified($signature);
var_dump($signatureVerified);
// bool true/false

$isSuccess = $callback->isSuccess();
var_dump($isSuccess);
// bool true/false
```

## Tes

```bash
composer test
```

## Perubahan

Silahkan lihat [CHANGELOG](CHANGELOG.md) untuk informasi perubahan pustaka.

## Kontribusi

Silahkan lihat [CONTRIBUTING](.github/CONTRIBUTING.md) untuk berkontribusi.

## Kerentanan Keamanan

Silahkan lihat [kebijakan keamanan kami](../../security/policy) untuk melaporkan kerentanan keamanan.

## Kredit

- [SemarangDev](https://github.com/semarangdev)
- [Semua Kontributor](../../contributors)

Terima kasih [package-skeleton-php](https://github.com/spatie/package-skeleton-php) untuk templat awal.

## Lisensi

Pustaka ini berlisensi MIT. Silahkan lihat [berkas lisensi](LICENSE.md) untuk informasi lebih banyak.
