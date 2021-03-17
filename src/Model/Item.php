<?php

namespace SemarangDev\Duitku\Model;

class Item extends Model
{
    public static $initiates = [
        'name',
        'price',
        'quantity',
    ];

    public function getTotalAttribute()
    {
        return $this->price;
    }
}
