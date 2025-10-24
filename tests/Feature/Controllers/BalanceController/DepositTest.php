<?php

namespace Tests\Feature\Controllers\BalanceController;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DepositTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function canDepositMoney(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/deposit', [
            'user_id' => $user->id,
            'amount' => 500.00,
            'comment' => 'Пополнение'
        ]);

        $response->assertStatus(200);
    }

    #[Test]
    public function cannotDepositIfUserNotFound(): void
    {
        $response = $this->postJson('/api/deposit', [
            'user_id' => 999,
            'amount' => 500.00,
            'comment' => 'Пополнение'
        ]);

        $response->assertStatus(404);
    }

    #[Test]
    public function cannotDepositIfIncorrectAmountValue(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/deposit', [
            'user_id' => $user->id,
            'amount' => 100.001,
            'comment' => 'Пополнение'
        ]);
        $response->assertStatus(422);
    }

    #[Test]
    public function cannotDepositIfNegativeOrZeroAmount(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/deposit', [
            'user_id' => $user->id,
            'amount' => -500.00,
            'comment' => 'Пополнение'
        ]);
        $response->assertStatus(422);

        $response = $this->postJson('/api/deposit', [
            'user_id' => $user->id,
            'amount' => 0,
            'comment' => 'Пополнение'
        ]);
        $response->assertStatus(422);
    }

    #[Test]
    public function cannotDepositWithoutAmount(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/deposit', [
            'user_id' => $user->id,
            'comment' => 'Пополнение'
        ]);
        $response->assertStatus(422);

        $response = $this->postJson('/api/deposit', [
            'user_id' => $user->id,
            'amount' => '',
            'comment' => 'Пополнение'
        ]);
        $response->assertStatus(422);
    }

    #[Test]
    public function cannotDepositWithoutComment(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/deposit', [
            'user_id' => $user->id,
            'amount' => 0,
        ]);
        $response->assertStatus(422);

        $response = $this->postJson('/api/deposit', [
            'user_id' => $user->id,
            'amount' => 0,
            'comment' => ''
        ]);
        $response->assertStatus(422);
    }
}
