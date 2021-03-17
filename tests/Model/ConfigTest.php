<?php

namespace SemarangDev\Duitku\Tests;

use PHPUnit\Framework\TestCase;
use SemarangDev\Duitku\Exception\DuitkuException;
use SemarangDev\Duitku\Model\Config;

class ConfigTest extends TestCase
{
    /** @test */
    public function cannot_initiate_class_with_empty_data()
    {
        $this->expectException(DuitkuException::class);
        $keys = implode(', ', Config::$initiates);
        $this->expectExceptionMessage("[$keys] is required to initiate the class");
        Config::make([]);
    }

    /** @test */
    public function cannot_initiate_class_with_incomplete_data()
    {
        $this->expectException(DuitkuException::class);
        $this->expectExceptionMessage('[merchant_code] is required to initiate the class');
        Config::make([
            'merchant_key' => 'DUITKU_MERCHANT_KEY',
            'base_url' => 'DUITKU_BASE_URL',
            'callback_url' => 'DUITKU_CALLBACK',
            'redirect_url' => 'DUITKU_REDIRECT_URL',
        ]);
    }

    /** @test */
    public function successfully_initiate_the_class()
    {
        $duitku = Config::make([
            'merchant_code' => 'DUITKU_MERCHANT_CODE',
            'merchant_key' => 'DUITKU_MERCHANT_KEY',
            'base_url' => 'DUITKU_BASE_URL',
            'callback_url' => 'DUITKU_CALLBACK',
            'redirect_url' => 'DUITKU_REDIRECT_URL',
        ]);
        $this->assertSame('DUITKU_MERCHANT_CODE', $duitku->merchant_code);
        $this->assertSame('DUITKU_MERCHANT_KEY', $duitku->merchant_key);
        $this->assertSame('DUITKU_BASE_URL', $duitku->base_url);
        $this->assertSame('DUITKU_CALLBACK', $duitku->callback_url);
        $this->assertSame('DUITKU_REDIRECT_URL', $duitku->redirect_url);
    }
}
