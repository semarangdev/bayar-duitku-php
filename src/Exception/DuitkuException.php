<?php

namespace SemarangDev\Duitku\Exception;

use Exception;

class DuitkuException extends Exception
{
    public function __construct(string $message)
    {
        $this->message = "DuitkuException: $message";
    }
}
