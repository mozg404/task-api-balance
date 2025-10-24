<?php

namespace App\Services;

use App\Exceptions\BalanceNotFoundException;
use App\Exceptions\InsufficientFundsException;
use App\Exceptions\TransferringToYourselfException;
use App\Models\Balance;

class BalanceOperator
{
    public function getBalance(int $userId): float
    {
        $balance = Balance::query()->where('user_id', $userId)->first();

        if (!$balance) {
            throw new BalanceNotFoundException();
        }

        return $balance->amount;
    }

    public function deposit(int $userId, float $amount): void
    {
        $balance = Balance::query()->firstOrCreate(['user_id' => $userId], ['amount' => 0]);
        $balance->increment('amount', $amount);
    }

    public function withdraw(int $userId, float $amount): void
    {
        if (!Balance::query()->where('user_id', $userId)->exists()) {
            throw new BalanceNotFoundException('Balance not found');
        }

        $balance = Balance::query()->where(['user_id' => $userId])->first();

        if (!$balance->hasEnough($amount)) {
            throw new InsufficientFundsException('Insufficient funds');
        }

        $balance->decrement('amount', $amount);
    }

    public function transfer(int $fromId, int $toId, float $amount): void
    {
        if ($fromId === $toId) {
            throw new TransferringToYourselfException();
        }

        $this->withdraw($fromId, $amount);
        $this->deposit($toId, $amount);
    }
}