<?php

namespace App\Exceptions;

use DomainException;

class BalanceNotFoundException extends DomainException
{
    protected $code = 409;
    protected $message = 'Balance not found';

}