<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class TransferDTO extends Data
{
    public function __construct(
        readonly public int $from_user_id,
        readonly public int $to_user_id,
        readonly float $amount,
        readonly public string $comment,
    ) {
    }

    public static function rules(): array
    {
        return [
            'from_user_id' => ['required', 'integer', 'gt:0'],
            'to_user_id' => ['required', 'integer', 'gt:0'],
            'amount' => ['required', 'decimal:0,2', 'gt:0'],
            'comment' => ['required', 'string', 'max:255'],
        ];
    }
}