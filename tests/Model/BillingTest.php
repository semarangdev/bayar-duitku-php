<?php

namespace SemarangDev\Duitku\Tests;

use PHPUnit\Framework\TestCase;
use SemarangDev\Duitku\Exception\DuitkuException;
use SemarangDev\Duitku\Model\Billing;
use SemarangDev\Duitku\Model\Item;

class BillingTest extends TestCase
{
    /** @test */
    public function cannot_initiate_class_with_empty_data()
    {
        $this->expectException(DuitkuException::class);
        $keys = implode(', ', Billing::$initiates);
        $this->expectExceptionMessage("[$keys] is required to initiate the class");
        Billing::make([]);
    }

    /** @test */
    public function cannot_initiate_class_with_incomplete_data()
    {
        $this->expectException(DuitkuException::class);
        $this->expectExceptionMessage('[items] is required to initiate the class');
        Billing::make([
            'order_id' => 'ORD.2020-01-01.X11',
            'customer' => 'Ganjar Pranowo',
            'email' => 'ganjar@pranowo.com',
            'phone' => '+6281222333444',
            'payment_method' => 'BT',
        ]);
    }

    /** @test */
    public function cannot_initiate_class_with_wrong_items()
    {
        $this->expectException(DuitkuException::class);
        $this->expectDeprecationMessage('Item must be instance of ' . Item::class);
        $item = Item::make([
            'name' => 'Token PLN',
            'price' => 20000,
            'quantity' => 20,
        ]);
        Billing::make([
            'order_id' => 'ORD.2020-01-01.X11',
            'customer' => 'Ganjar Pranowo',
            'email' => 'ganjar@pranowo.com',
            'phone' => '+6281222333444',
            'items' => [$item, 1, 2, 'three'],
            'payment_method' => 'BT',
        ]);
    }

    /** @test */
    public function successfully_initiate_the_class()
    {
        $items = [
            Item::make([
                'name' => 'Sandal Jepit',
                'price' => 20000,
                'quantity' => 2,
            ]),
            Item::make([
                'name' => 'Sepatu Bebas',
                'price' => 200000,
                'quantity' => 1,
            ]),
        ];
        $billing = Billing::make([
            'order_id' => 'ORD.2020-01-01.X11',
            'customer' => 'Ganjar Pranowo',
            'email' => 'ganjar@pranowo.com',
            'phone' => '+6281222333444',
            'items' => $items,
            'payment_method' => 'BT',
        ]);
        $this->assertSame('ORD.2020-01-01.X11', $billing->order_id);
        $this->assertSame('Ganjar Pranowo', $billing->customer);
        $this->assertSame('ganjar@pranowo.com', $billing->email);
        $this->assertSame('+6281222333444', $billing->phone);
        $this->assertSame($items, $billing->items);
        $this->assertSame(220000, $billing->total);
    }
}
