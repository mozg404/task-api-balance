<?php

namespace App\Enum;

enum TransactionType: string
{
    case Deposit = 'deposit';
    case Withdraw = 'withdraw';
    case TransferIn = 'transfer_in';
    case TransferOut = 'transfer_out';
}