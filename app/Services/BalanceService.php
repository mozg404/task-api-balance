<?php

namespace App\Services;

use App\DTO\DepositDTO;
use App\DTO\WithdrawDTO;
use App\Enum\TransactionType;
use App\Exceptions\BalanceNotFoundException;
use App\Exceptions\InsufficientFundsException;
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
        $transaction->save();
    }

    public function withdraw(WithdrawDTO $dto): void
    {
        $this->ensureExistUser($dto->user_id);
        $this->ensureExistBalance($dto->user_id);

        $balance = Balance::where(['user_id' => $dto->user_id])->first();

        if (!$balance->hasEnough($dto->amount)) {
            throw new InsufficientFundsException('Insufficient funds');
        }

        $balance->decrement('amount', $dto->amount);

        $transaction = new Transaction();
        $transaction->user_id = $dto->user_id;
        $transaction->type = TransactionType::Withdraw;
        $transaction->amount = -$dto->amount;
        $transaction->comment = $dto->comment;
        $transaction->save();
    }

    private function ensureExistUser(int $id, string $message = 'User not found'): void
    {
        if (!User::query()->where('id', $id)->exists()) {
            throw new ModelNotFoundException($message);
        }
    }

    private function ensureExistBalance(int $id, string $message = 'Balance not found'): void
    {
        if (!Balance::query()->where('user_id', $id)->exists()) {
            throw new BalanceNotFoundException($message);
        }
    }
}