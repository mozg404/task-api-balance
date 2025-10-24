<?php

namespace App\Http\Controllers;

use App\DTO\BalanceRequestDto;
use App\DTO\DepositDto;
use App\DTO\TransferDto;
use App\DTO\WithdrawDto;
use App\Enum\ResponseErrorCode;
use App\Enum\ResponseStatus;
use App\Exceptions\BalanceNotFoundException;
use App\Exceptions\InsufficientFundsException;
use App\Exceptions\TransferringToYourselfException;
use App\Services\BalanceService;
use Exception;
use Illuminate\Http\JsonResponse;

class BalanceController extends Controller
{
    public function balance(int $userId, BalanceService $service): JsonResponse
    {
        try {
            $resultDto = $service->getBalance(BalanceRequestDto::validateAndCreate(['user_id' => $userId]));

            return $this->jsonSuccess([
                'user_id' => $resultDto->user_id,
                'balance' => number_format($resultDto->balance, 2, '.', ''),
            ]);
        } catch (BalanceNotFoundException $e) {
            return $this->jsonError($e);
        }
    }
    
    public function deposit(DepositDto $dto, BalanceService $service): JsonResponse
    {
        $service->deposit($dto);

        return $this->jsonSuccess();
    }

    public function withdraw(WithdrawDto $dto, BalanceService $service): JsonResponse
    {
        try {
            $service->withdraw($dto);

            return $this->jsonSuccess();
        } catch (BalanceNotFoundException|InsufficientFundsException $e) {
            return $this->jsonError($e);
        }
    }

    public function transfer(TransferDto $dto, BalanceService $service): JsonResponse
    {
        try {
            $service->transfer($dto);

            return $this->jsonSuccess();
        } catch (BalanceNotFoundException|InsufficientFundsException|TransferringToYourselfException $e) {
            return $this->jsonError($e);
        }
    }

    private function jsonSuccess(array $data = []): JsonResponse
    {
        return response()->json(array_merge(['status' => ResponseStatus::Success->value], $data));
    }

    private function jsonError(Exception $e, int $statusCode = 409): JsonResponse
    {
        return response()->json([
            'status' => ResponseStatus::Error->value,
            'code' => ResponseErrorCode::fromException($e)->value,
            'message' => $e->getMessage(),
        ], $statusCode);
    }
}
