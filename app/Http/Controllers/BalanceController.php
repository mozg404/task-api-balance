<?php

namespace App\Http\Controllers;

use App\DTO\DepositDTO;
use App\Services\BalanceService;
use Illuminate\Http\Response;

class BalanceController extends Controller
{
    public function deposit(DepositDTO $dto, BalanceService $service): Response
    {
        $service->deposit($dto);

        return response()->noContent(200);
    }
}
