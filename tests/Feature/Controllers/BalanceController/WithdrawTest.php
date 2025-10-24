<?php

namespace Controllers\BalanceController;

use App\Models\Balance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WithdrawTest extends TestCase
{
    public const string URL = '/api/withdraw';

    use RefreshDatabase;

    #[Test]
    public function canWithdraw(): void
    {
        $user = User::factory()->create();
        Balance::factory()->for($user)->withAmount(501.00)->create();

        $response = $this->postJson(self::URL, [
            'user_id' => $user->id,
            'amount' => 500.00,
            'comment' => 'Списание'
        ]);

        $response->assertStatus(200);
    }

    #[Test]
    public function cannotWithdrawIfNotBalance(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(self::URL, [
            'user_id' => $user->id,
            'amount' => 500.00,
            'comment' => 'Списание'
        ]);

        $response->assertStatus(409);
    }

    #[Test]
    public function cannotWithdrawIfInsufficientFunds(): void
    {
        $user = User::factory()->create();
        Balance::factory()->for($user)->withAmount(200.00);

        $response = $this->postJson(self::URL, [
            'user_id' => $user->id,
            'amount' => 500.00,
            'comment' => 'Списание'
        ]);

        $response->assertStatus(409);
    }

    #[Test]
    public function cannotWithdrawIfUserNotFound(): void
    {
        $response = $this->postJson(self::URL, [
            'user_id' => 999,
            'amount' => 500.00,
            'comment' => 'Пополнение'
        ]);

        $response->assertStatus(404);
    }

    #[Test]
    public function cannotWithdrawIfIncorrectAmountValue(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(self::URL, [
            'user_id' => $user->id,
            'amount' => 100.001,
            'comment' => 'Пополнение'
        ]);
        $response->assertStatus(422);
    }

    #[Test]
    public function cannotWithdrawIfNegativeOrZeroAmount(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(self::URL, [
            'user_id' => $user->id,
            'amount' => -500.00,
            'comment' => 'Пополнение'
        ]);
        $response->assertStatus(422);

        $response = $this->postJson(self::URL, [
            'user_id' => $user->id,
            'amount' => 0,
            'comment' => 'Пополнение'
        ]);
        $response->assertStatus(422);
    }

    #[Test]
    public function cannotWithdrawWithoutAmount(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(self::URL, [
            'user_id' => $user->id,
            'comment' => 'Пополнение'
        ]);
        $response->assertStatus(422);

        $response = $this->postJson(self::URL, [
            'user_id' => $user->id,
            'amount' => '',
            'comment' => 'Пополнение'
        ]);
        $response->assertStatus(422);
    }

    #[Test]
    public function cannotWithdrawWithoutComment(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(self::URL, [
            'user_id' => $user->id,
            'amount' => 0,
        ]);
        $response->assertStatus(422);

        $response = $this->postJson(self::URL, [
            'user_id' => $user->id,
            'amount' => 0,
            'comment' => ''
        ]);
        $response->assertStatus(422);
    }
}
