<?php

namespace App\Exceptions\Api;

use Exception;

abstract class ApiException extends Exception
{
    protected int $status = 400;

    public function getStatus(): int
    {
        return $this->status;
    }
}
