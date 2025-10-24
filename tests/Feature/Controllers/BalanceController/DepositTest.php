<?php

namespace Tests\Feature\Controllers\BalanceController;

use App\Enum\ResponseErrorCode;
use App\Enum\ResponseStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DepositTest extends TestCase
{
    use RefreshDatabase;

    public const string URL = '/api/deposit';

    #[Test]
    public function canDepositMoney(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(self::URL, [
            'user_id' => $user->id,
            'amount' => 500.00,
            'comment' => 'Пополнение'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => ResponseStatus::Success->value,
            ]);
    }

    #[Test]
    public function cannotDepositIfUserNotFound(): void
    {
        $response = $this->postJson(self::URL, [
            'user_id' => 999,
            'amount' => 500.00,
            'comment' => 'Пополнение'
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'status' => ResponseStatus::Error->value,
                'code' => ResponseErrorCode::NotFound->value,
            ]);
    }

    #[Test]
    public function cannotDepositIfIncorrectAmountValue(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(self::URL, [
            'user_id' => $user->id,
            'amount' => 100.001,
            'comment' => 'Пополнение'
        ]);
        $response->assertStatus(422)
            ->assertJson([
                'status' => ResponseStatus::Error->value,
                'code' => ResponseErrorCode::ValidationError->value,
            ]);
    }

    #[Test]
    public function cannotDepositIfNegativeOrZeroAmount(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(self::URL, [
            'user_id' => $user->id,
            'amount' => -500.00,
            'comment' => 'Пополнение'
        ]);
        $response->assertStatus(422)
            ->assertJson([
                'status' => ResponseStatus::Error->value,
                'code' => ResponseErrorCode::ValidationError->value,
            ]);

        $response = $this->postJson(self::URL, [
            'user_id' => $user->id,
            'amount' => 0,
            'comment' => 'Пополнение'
        ]);
        $response->assertStatus(422)
            ->assertJson([
                'status' => ResponseStatus::Error->value,
                'code' => ResponseErrorCode::ValidationError->value,
            ]);
    }

    #[Test]
    public function cannotDepositWithoutAmount(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(self::URL, [
            'user_id' => $user->id,
            'comment' => 'Пополнение'
        ]);
        $response->assertStatus(422)
            ->assertJson([
                'status' => ResponseStatus::Error->value,
                'code' => ResponseErrorCode::ValidationError->value,
            ]);

        $response = $this->postJson(self::URL, [
            'user_id' => $user->id,
            'amount' => '',
            'comment' => 'Пополнение'
        ]);
        $response->assertStatus(422)
            ->assertJson([
                'status' => ResponseStatus::Error->value,
                'code' => ResponseErrorCode::ValidationError->value,
            ]);
    }

    #[Test]
    public function cannotDepositWithoutComment(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(self::URL, [
            'user_id' => $user->id,
            'amount' => 0,
        ]);
        $response->assertStatus(422)
            ->assertJson([
                'status' => ResponseStatus::Error->value,
                'code' => ResponseErrorCode::ValidationError->value,
            ]);

        $response = $this->postJson(self::URL, [
            'user_id' => $user->id,
            'amount' => 0,
            'comment' => ''
        ]);
        $response->assertStatus(422)
            ->assertJson([
                'status' => ResponseStatus::Error->value,
                'code' => ResponseErrorCode::ValidationError->value,
            ]);
    }
}
