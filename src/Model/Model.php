<?php

namespace SemarangDev\Duitku\Model;

use SemarangDev\Duitku\Exception\DuitkuException;

class Model
{
    public $attributes = [];

    public static $initiates = [];

    public function __construct(array $data)
    {
        $incompleteKeys = [];

        foreach (static::$initiates as $key) {
            array_key_exists($key, $data)
                ? ($this->setAttribute($key, $data[$key]))
                : ($incompleteKeys[] = $key);
        }

        if (count($incompleteKeys) > 0) {
            $keys = implode(', ', $incompleteKeys);

            throw new DuitkuException("[$keys] is required to initiate the class");
        }
    }

    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    public function getAttribute($key)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }

        if (method_exists($this, 'get' . ucfirst($key) . 'Attribute')) {
            return $this->{'get' . ucfirst($key) . 'Attribute'}();
        }

        return null;
    }

    public function __set($key, $value)
    {
        return $this->setAttribute($key, $value);
    }

    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    public static function make(array $data): self
    {
        return new static($data);
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}
