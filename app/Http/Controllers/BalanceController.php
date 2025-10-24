<?php

namespace App\Http\Controllers;

use App\DTO\DepositDTO;
use App\DTO\TransferDTO;
use App\DTO\WithdrawDTO;
use App\Exceptions\BalanceNotFoundException;
use App\Exceptions\InsufficientFundsException;
use App\Exceptions\TransferringToYourselfException;
use App\Services\BalanceService;
use Illuminate\Http\Response;

class BalanceController extends Controller
{
    public function deposit(DepositDTO $dto, BalanceService $service): Response
    {
        $service->deposit($dto);

        return response()->noContent(200);
    }

    public function withdraw(WithdrawDTO $dto, BalanceService $service): Response
    {
        try {
            $service->withdraw($dto);
        } catch (BalanceNotFoundException|InsufficientFundsException $e) {
            return response()->noContent(409);
        }

        return response()->noContent(200);
    }

    public function transfer(TransferDTO $dto, BalanceService $service): Response
    {
        try {
            $service->transfer($dto);
        } catch (BalanceNotFoundException|InsufficientFundsException|TransferringToYourselfException $e) {
            return response()->noContent(409);
        }

        return response()->noContent(200);
    }
}
