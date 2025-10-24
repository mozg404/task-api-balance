<?php

namespace App\Exceptions;

use DomainException;

class InsufficientFundsException extends DomainException
{
    protected $code = 409;
    protected $message = 'Insufficient funds';
}