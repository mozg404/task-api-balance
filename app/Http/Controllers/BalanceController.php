<?php

namespace App\Http\Controllers;

use App\DTO\BalanceRequestDto;
use App\DTO\DepositDto;
use App\DTO\TransferDto;
use App\DTO\WithdrawDTO;
use App\Exceptions\BalanceNotFoundException;
use App\Exceptions\InsufficientFundsException;
use App\Exceptions\TransferringToYourselfException;
use App\Services\BalanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class BalanceController extends Controller
{
    public function balance(int $userId, BalanceService $service): Response|JsonResponse
    {
        try {
            $resultDto = $service->getBalance(BalanceRequestDto::validateAndCreate(['user_id' => $userId]));

            return response()->json([
                'user_id' => $resultDto->user_id,
                'balance' => $resultDto->balance,
            ]);
        } catch (BalanceNotFoundException $e) {
            return response()->noContent(409);
        }
    }
    
    public function deposit(DepositDto $dto, BalanceService $service): Response
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

    public function transfer(TransferDto $dto, BalanceService $service): Response
    {
        try {
            $service->transfer($dto);
        } catch (BalanceNotFoundException|InsufficientFundsException|TransferringToYourselfException $e) {
            return response()->noContent(409);
        }

        return response()->noContent(200);
    }
}
