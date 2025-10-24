<?php

namespace App\Enum;

use App\Exceptions\BalanceNotFoundException;
use App\Exceptions\InsufficientFundsException;
use App\Exceptions\TransferringToYourselfException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

enum ResponseErrorCode: string
{
    case BalanceNotFound = 'balance_not_found';
    case NotFound = 'not_found';
    case ValidationError = 'validation_error';
    case InsufficientFunds = 'insufficient_funds';
    case TransferToYourself = 'transfer_to_yourself';

    public static function fromException(Exception $exception): self
    {
        return match ($exception::class) {
            BalanceNotFoundException::class => self::BalanceNotFound,
            ModelNotFoundException::class, NotFoundHttpException::class => self::NotFound,
            ValidationException::class => self::ValidationError,
            InsufficientFundsException::class => self::InsufficientFunds,
            TransferringToYourselfException::class => self::TransferToYourself,
        };
    }
}

