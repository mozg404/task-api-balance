<?php

namespace App\Services;

use App\DTO\BalanceRequestDto;
use App\DTO\BalanceResultDto;
use App\DTO\DepositDTO;
use App\DTO\TransferDTO;
use App\DTO\WithdrawDTO;
use App\Enum\TransactionType;
use App\Exceptions\BalanceNotFoundException;
use App\Exceptions\InsufficientFundsException;
use App\Exceptions\TransferringToYourselfException;
use App\Models\Balance;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BalanceService
{
    public function getBalance(BalanceRequestDto $dto): BalanceResultDto
    {
        $this->ensureExistUser($dto->user_id);

        $balance = Balance::where(['user_id' => $dto->user_id])->first();

        if (!isset($balance)) {
            throw new BalanceNotFoundException();
        }

        return new BalanceResultDto($dto->user_id, $balance->amount);
    }

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

    public function transfer(TransferDTO $dto): void
    {
        $this->ensureExistUser($dto->from_user_id);
        $this->ensureExistUser($dto->to_user_id);

        if ($dto->from_user_id === $dto->to_user_id) {
            throw new TransferringToYourselfException();
        }

        $balanceSender = Balance::where(['user_id' => $dto->from_user_id])->first();

        if (!isset($balanceSender)) {
            throw new BalanceNotFoundException();
        }

        // Проверка баланса у отправителя
        if (!$balanceSender->hasEnough($dto->amount)) {
            throw new InsufficientFundsException('Insufficient funds');
        }

        // Если нет баланса у получателя - создаем
        $balanceRecipient = Balance::firstOrCreate(['user_id' => $dto->to_user_id], ['amount' => $dto->amount]);

        // Списываем у отправителя
        $balanceSender->decrement('amount', $dto->amount);

        // Добавляем получателю
        $balanceRecipient->increment('amount', $dto->amount);

        $transaction = new Transaction();
        $transaction->user_id = $dto->from_user_id;
        $transaction->related_user_id = $dto->to_user_id;
        $transaction->type = TransactionType::TransferOut;
        $transaction->amount = -$dto->amount;
        $transaction->comment = $dto->comment;
        $transaction->save();

        $transaction = new Transaction();
        $transaction->user_id = $dto->to_user_id;
        $transaction->related_user_id = $dto->from_user_id;
        $transaction->type = TransactionType::TransferOut;
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