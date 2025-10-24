<?php

namespace App\Exceptions;

use DomainException;

class TransferringToYourselfException extends DomainException
{
    protected $code = 409;
    protected $message = 'Transferring from yourself self';
}