<?php

namespace Tests\Feature\Services;

use App\Enum\TransactionType;
use App\Models\User;
use App\Services\TransactionRegistrar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TransactionRegistrarTest extends TestCase
{
    use RefreshDatabase;

    private TransactionRegistrar $registrar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registrar = $this->app->get(TransactionRegistrar::class);
    }

    #[Test]
    public function canRegisterDepositOperation(): void
    {
        $user = User::factory()->create();
        $amount = 20.00;
        $comment = 'Пополнение';

        $this->registrar->registerDepositOperation($user->id, $amount, $comment);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => TransactionType::Deposit,
            'amount' => $amount,
            'comment' => $comment,
            'related_user_id' => null,
        ]);
    }

    #[Test]
    public function canRegisterWithdrawOperation(): void
    {
        $user = User::factory()->create();
        $amount = 20.00;
        $comment = 'Списание';

        $this->registrar->registerWithdrawOperation($user->id, $amount, $comment);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => TransactionType::Withdraw,
            'amount' => -$amount,
            'comment' => $comment,
            'related_user_id' => null,
        ]);
    }

    #[Test]
    public function canRegisterTransferOperation(): void
    {
        $userSender = User::factory()->create();
        $userRecipient = User::factory()->create();
        $amount = 20.00;
        $comment = 'Списание';

        $this->registrar->registerTransferOperation($userSender->id, $userRecipient->id, $amount, $comment);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $userSender->id,
            'type' => TransactionType::TransferOut,
            'amount' => -$amount,
            'comment' => $comment,
            'related_user_id' => $userRecipient->id,
        ]);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $userRecipient->id,
            'type' => TransactionType::TransferIn,
            'amount' => $amount,
            'comment' => $comment,
            'related_user_id' => $userSender->id,
        ]);
    }
}
