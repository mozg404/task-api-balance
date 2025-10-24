<?php

namespace App\Services;

use App\DTO\DepositDTO;
use App\Enum\TransactionType;
use App\Models\Balance;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BalanceService
{
    public function deposit(DepositDTO $dto): void
    {
        $this->ensureExistUser($dto->user_id);

        $balance = Balance::firstOrCreate(['user_id' => $dto->user_id], ['amount' => 0]);
        $balance->increment('amount', $dto->amount);

        $transaction = new Transaction();
        $transaction->user_id = $dto->user_id;
        $transaction->type = TransactionType::Deposit;
        $transaction->amount = $dto->amount;
        $transaction->comment = $dto->comment;
    }

    private function ensureExistUser(int $id, string $message = 'User not found'): void
    {
        if (!User::query()->where('id', $id)->exists()) {
            throw new ModelNotFoundException($message);
        }
    }
}