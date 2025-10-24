<?php

namespace App\Services;

use App\Enum\TransactionType;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionRegistrar
{
    public function registerDepositOperation(int $userId, float $amount, string $comment): void
    {
        $transaction = new Transaction();
        $transaction->user_id = $userId;
        $transaction->amount = $amount;
        $transaction->comment = $comment;
        $transaction->type = TransactionType::Deposit;
        $transaction->save();
    }

    public function registerWithdrawOperation(int $userId, float $amount, string $comment): void
    {
        $transaction = new Transaction();
        $transaction->user_id = $userId;
        $transaction->amount = -$amount;
        $transaction->comment = $comment;
        $transaction->type = TransactionType::Withdraw;
        $transaction->save();
    }

    public function registerTransferOperation(int $fromId, int $toId, float $amount, string $comment): void
    {
        DB::transaction(function () use ($fromId, $toId, $amount, $comment) {
            $transaction = new Transaction();
            $transaction->user_id = $fromId;
            $transaction->related_user_id = $toId;
            $transaction->amount = -$amount;
            $transaction->comment = $comment;
            $transaction->type = TransactionType::TransferOut;
            $transaction->save();

            $transaction = new Transaction();
            $transaction->user_id = $toId;
            $transaction->related_user_id = $fromId;
            $transaction->amount = $amount;
            $transaction->comment = $comment;
            $transaction->type = TransactionType::TransferIn;
            $transaction->save();
        });
    }
}