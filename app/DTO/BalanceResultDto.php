<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class BalanceResultDto extends Data
{
    public function __construct(
        readonly public int $user_id,
        readonly float $balance,
    ) {
    }
}