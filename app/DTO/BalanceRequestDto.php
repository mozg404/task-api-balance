<?php

namespace App\DTO;

use Spatie\LaravelData\Data;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;

class BalanceRequestDto extends Data
{
    public function __construct(
        readonly public int $user_id,
    ) {
    }

    public static function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'gt:0'],
        ];
    }
}