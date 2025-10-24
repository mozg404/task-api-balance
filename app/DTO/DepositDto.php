<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class DepositDto extends Data
{
    public function __construct(
        readonly public int $user_id,
        readonly public int $amount,
        readonly public string $comment,
    ) {
    }

    public static function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'gt:0'],
            'amount' => ['required', 'decimal:0,2', 'gt:0'],
            'comment' => ['required', 'string', 'max:255'],
        ];
    }
}