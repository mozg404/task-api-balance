<?php

namespace Controllers\BalanceController;

use App\Models\Balance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BalanceTest extends TestCase
{
    use RefreshDatabase;

    public const string URL = '/api/balance';

    public function getBalanceRequest(int $userId): TestResponse
    {
        return $this->getJson(self::URL . '/' . $userId);
    }

    #[Test]
    public function canGetBalance(): void
    {
        $user = User::factory()->create();
        Balance::factory()->for($user)->withAmount(100)->create();

        $response = $this->getBalanceRequest($user->id);

        $response->assertStatus(200)
            ->assertJson([
                'user_id' => $user->id,
                'balance' => 100.00
            ]);
    }

    #[Test]
    public function cannotGetBalanceIfUserNotExists(): void
    {
        $response = $this->getBalanceRequest(999);
        $response->assertStatus(404);
    }

    #[Test]
    public function cannotGetBalanceIfUserNotBalance(): void
    {
        $user = User::factory()->create();

        $response = $this->getBalanceRequest($user->id);
        $response->assertStatus(409);
    }
}
