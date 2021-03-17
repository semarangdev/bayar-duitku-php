<?php

namespace SemarangDev\Duitku\Model;

use SemarangDev\Duitku\Exception\DuitkuException;

class Billing extends Model
{
    public static $initiates = [
        'order_id',
        'customer',
        'email',
        'phone',
        'items',
        'payment_method',
    ];

    public static function make(array $data): self
    {
        foreach (@$data['items'] ?? [] as $item) {
            if (@get_class((object) $item) != Item::class) {
                throw new DuitkuException('Item must be instance of ' . Item::class);
            }
        }

        return new static($data);
    }

    public function getTotalAttribute()
    {
        $amounts = array_map(function (Item $item) {
            return $item->total;
        }, $this->items);

        return array_sum($amounts);
    }

    public function toArray(): array
    {
        $arr = parent::toArray();
        $arr['items'] = array_map(function ($item) {
            return $item->toArray();
        }, $this->items);

        return $arr;
    }
}
