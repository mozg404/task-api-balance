<?php

namespace App\Services;

use App\DTO\BalanceRequestDto;
use App\DTO\BalanceResultDto;
use App\DTO\DepositDTO;
use App\DTO\TransferDTO;
use App\DTO\WithdrawDTO;
use App\Exceptions\BalanceNotFoundException;
use App\Models\Balance;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class BalanceService
{
    public function __construct(
        readonly private TransactionRegistrar $registrar,
        readonly private BalanceOperator $operator,
    ) {
    }

    public function getBalance(BalanceRequestDto $dto): BalanceResultDto
    {
        $this->ensureExistUser($dto->user_id);
        $balance = $this->operator->getBalance($dto->user_id);

        return new BalanceResultDto($dto->user_id, $balance);
    }

    public function deposit(DepositDTO $dto): void
    {
        $this->ensureExistUser($dto->user_id);

        DB::transaction(function () use ($dto) {
            $this->operator->deposit($dto->user_id, $dto->amount);
            $this->registrar->registerDepositOperation($dto->user_id, $dto->amount, $dto->amount);
        });
    }

    public function withdraw(WithdrawDTO $dto): void
    {
        $this->ensureExistUser($dto->user_id);

        DB::transaction(function () use ($dto) {
            $this->operator->withdraw($dto->user_id, $dto->amount);
            $this->registrar->registerWithdrawOperation($dto->user_id, $dto->amount, $dto->amount);
        });
    }

    public function transfer(TransferDTO $dto): void
    {
        $this->ensureExistUser($dto->from_user_id);
        $this->ensureExistUser($dto->to_user_id);

        DB::transaction(function () use ($dto) {
            $this->operator->transfer($dto->from_user_id, $dto->to_user_id, $dto->amount);
            $this->registrar->registerTransferOperation($dto->from_user_id, $dto->to_user_id, $dto->amount, $dto->comment);
        });
    }

    private function ensureExistUser(int $id, string $message = 'User not found'): void
    {
        if (!User::query()->where('id', $id)->exists()) {
            throw new ModelNotFoundException($message);
        }
    }
}