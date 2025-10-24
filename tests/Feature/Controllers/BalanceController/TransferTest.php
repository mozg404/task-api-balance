<?php

namespace Controllers\BalanceController;

use App\Models\Balance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TransferTest extends TestCase
{
    public const string URL = '/api/transfer';

    use RefreshDatabase;

    #[Test]
    public function canTransfer(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Balance::factory()->for($user1)->withAmount(500.00)->create();
        Balance::factory()->for($user2)->withAmount(100.00)->create();

        $response = $this->postJson(self::URL, [
            'from_user_id' => $user1->id,
            'to_user_id' => $user2->id,
            'amount' => 200.00,
            'comment' => 'На мороженное'
        ]);

        $response->assertStatus(200);
    }

    #[Test]
    public function cannotTransferYourself(): void
    {
        $user1 = User::factory()->create();
        Balance::factory()->for($user1)->withAmount(500.00)->create();

        $response = $this->postJson(self::URL, [
            'from_user_id' => $user1->id,
            'to_user_id' => $user1->id,
            'amount' => 200.00,
            'comment' => 'На мороженное'
        ]);

        $response->assertStatus(409);
    }

    #[Test]
    public function cannotTransferIfSenderNotFound(): void
    {
        $user = User::factory()->create();
        Balance::factory()->for($user)->withAmount(500.00)->create();

        $response = $this->postJson(self::URL, [
            'from_user_id' => 999,
            'to_user_id' => $user->id,
            'amount' => 200.00,
            'comment' => 'На мороженное'
        ]);

        $response->assertStatus(404);
    }

    #[Test]
    public function cannotTransferIfRecipientNotFound(): void
    {
        $user = User::factory()->create();
        Balance::factory()->for($user)->withAmount(500.00)->create();

        $response = $this->postJson(self::URL, [
            'from_user_id' => $user->id,
            'to_user_id' => 999,
            'amount' => 200.00,
            'comment' => 'На мороженное'
        ]);

        $response->assertStatus(404);
    }

    #[Test]
    public function cannotTransferIfSenderNotBalance(): void
    {
        $userSender = User::factory()->create();
        $userRecipient = User::factory()->create();
        Balance::factory()->for($userRecipient)->withAmount(100.00)->create();

        $response = $this->postJson(self::URL, [
            'from_user_id' => $userSender->id,
            'to_user_id' => $userRecipient->id,
            'amount' => 500.00,
            'comment' => 'На мороженное'
        ]);

        $response->assertStatus(409);
    }

    #[Test]
    public function cannotTransferIfSenderInsufficientFunds(): void
    {
        $userSender = User::factory()->create();
        $userRecipient = User::factory()->create();
        Balance::factory()->for($userSender)->withAmount(200.00)->create();
        Balance::factory()->for($userRecipient)->withAmount(100.00)->create();

        $response = $this->postJson(self::URL, [
            'from_user_id' => $userSender->id,
            'to_user_id' => $userRecipient->id,
            'amount' => 500.00,
            'comment' => 'На мороженное'
        ]);

        $response->assertStatus(409);
    }

    #[Test]
    public function canTransferIfRecipientNotBalance(): void
    {
        $userSender = User::factory()->create();
        $userRecipient = User::factory()->create();
        Balance::factory()->for($userSender)->withAmount(600.00)->create();

        $response = $this->postJson(self::URL, [
            'from_user_id' => $userSender->id,
            'to_user_id' => $userRecipient->id,
            'amount' => 500.00,
            'comment' => 'На мороженное'
        ]);

        $response->assertStatus(200);
    }

    #[Test]
    public function cannotTransferIfIncorrectAmountValue(): void
    {
        $userSender = User::factory()->create();
        $userRecipient = User::factory()->create();
        Balance::factory()->for($userSender)->withAmount(200.00)->create();
        Balance::factory()->for($userRecipient)->withAmount(100.00)->create();

        $response = $this->postJson(self::URL, [
            'from_user_id' => $userSender->id,
            'to_user_id' => $userRecipient->id,
            'amount' => 100.001,
            'comment' => 'На мороженное'
        ]);
        $response->assertStatus(422);

        $response = $this->postJson(self::URL, [
            'from_user_id' => $userSender->id,
            'to_user_id' => $userRecipient->id,
            'amount' => 'amount',
            'comment' => 'На мороженное'
        ]);
        $response->assertStatus(422);

        $response = $this->postJson(self::URL, [
            'from_user_id' => $userSender->id,
            'to_user_id' => $userRecipient->id,
            'amount' => 0,
            'comment' => 'На мороженное'
        ]);
        $response->assertStatus(422);

        $response = $this->postJson(self::URL, [
            'from_user_id' => $userSender->id,
            'to_user_id' => $userRecipient->id,
            'amount' => -100,
            'comment' => 'На мороженное'
        ]);
        $response->assertStatus(422);
    }

    #[Test]
    public function cannotTransferWithoutAmount(): void
    {
        $userSender = User::factory()->create();
        $userRecipient = User::factory()->create();
        Balance::factory()->for($userSender)->withAmount(200.00)->create();
        Balance::factory()->for($userRecipient)->withAmount(100.00)->create();

        $response = $this->postJson(self::URL, [
            'from_user_id' => $userSender->id,
            'to_user_id' => $userRecipient->id,
            'amount' => '',
            'comment' => 'На мороженное'
        ]);
        $response->assertStatus(422);

        $response = $this->postJson(self::URL, [
            'from_user_id' => $userSender->id,
            'to_user_id' => $userRecipient->id,
            'comment' => 'На мороженное'
        ]);
        $response->assertStatus(422);
    }

    #[Test]
    public function cannotTransferWithoutComment(): void
    {
        $userSender = User::factory()->create();
        $userRecipient = User::factory()->create();
        Balance::factory()->for($userSender)->withAmount(200.00)->create();
        Balance::factory()->for($userRecipient)->withAmount(100.00)->create();

        $response = $this->postJson(self::URL, [
            'from_user_id' => $userSender->id,
            'to_user_id' => $userRecipient->id,
            'amount' => 100,
            'comment' => ''
        ]);
        $response->assertStatus(422);

        $response = $this->postJson(self::URL, [
            'from_user_id' => $userSender->id,
            'to_user_id' => $userRecipient->id,
            'amount' => 100,
        ]);
        $response->assertStatus(422);
    }
}
