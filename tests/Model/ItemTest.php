<?php

namespace SemarangDev\Duitku\Tests;

use PHPUnit\Framework\TestCase;
use SemarangDev\Duitku\Exception\DuitkuException;
use SemarangDev\Duitku\Model\Item;

class ItemTest extends TestCase
{
    /** @test */
    public function cannot_initiate_class_with_empty_data()
    {
        $this->expectException(DuitkuException::class);
        $keys = implode(', ', Item::$initiates);
        $this->expectExceptionMessage("[$keys] is required to initiate the class");
        Item::make([]);
    }

    /** @test */
    public function cannot_initiate_class_with_incomplete_data()
    {
        $this->expectException(DuitkuException::class);
        $this->expectExceptionMessage('[price] is required to initiate the class');
        Item::make([
            'name' => 'Token PLN',
            'quantity' => 20,
        ]);
    }

    /** @test */
    public function successfully_initiate_the_class()
    {
        $item = Item::make([
            'name' => 'Token PLN',
            'price' => 1000,
            'quantity' => 20,
        ]);
        $this->assertSame('Token PLN', $item->name);
        $this->assertSame(1000, $item->price);
        $this->assertSame(20, $item->quantity);
        $this->assertSame(20000, $item->total);
        $item->price = 2000;
        $this->assertSame(2000, $item->price);
        $this->assertSame(40000, $item->total);
        $this->assertSame(null, $item->prince);
    }
}
