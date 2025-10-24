<?php

namespace Services;

use App\Exceptions\BalanceNotFoundException;
use App\Exceptions\InsufficientFundsException;
use App\Exceptions\TransferringToYourselfException;
use App\Models\Balance;
use App\Models\User;
use App\Services\BalanceOperator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BalanceOperatorTest extends TestCase
{
    use RefreshDatabase;

    private BalanceOperator $operator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operator = $this->app->get(BalanceOperator::class);
    }

    #[Test]
    public function canGetBalance(): void
    {
        $balance = 100.00;
        $user = User::factory()->create();
        Balance::factory()->for($user)->withAmount($balance)->create();

        $balance = $this->operator->getBalance($user->id);

        $this->assertEquals($balance, $balance);
    }

    #[Test]
    public function cannotGetBalanceIfNotExists(): void
    {
        $user = User::factory()->create();

        $this->expectException(BalanceNotFoundException::class);
        $balance = $this->operator->getBalance($user->id);
    }

    #[Test]
    public function canDepositWithExistingBalance(): void
    {
        $startBalance = 100.00;
        $amount = 20.00;
        $user = User::factory()->create();
        Balance::factory()->for($user)->withAmount($startBalance)->create();

        $this->operator->deposit($user->id, $amount);

        $this->assertDatabaseHas('balances', [
            'user_id' => $user->id,
            'amount' => $startBalance + $amount,
        ]);
    }

    #[Test]
    public function canDepositWithoutBalance(): void
    {
        $amount = 20.00;
        $user = User::factory()->create();

        $this->operator->deposit($user->id, $amount);

        $this->assertDatabaseHas('balances', [
            'user_id' => $user->id,
            'amount' => $amount,
        ]);
    }

    #[Test]
    public function canWithdraw(): void
    {
        $startBalance = 100.00;
        $amount = 20.00;
        $user = User::factory()->create();
        Balance::factory()->for($user)->withAmount($startBalance)->create();

        $this->operator->withdraw($user->id, $amount);

        $this->assertDatabaseHas('balances', [
            'user_id' => $user->id,
            'amount' => $startBalance - $amount,
        ]);
    }

    #[Test]
    public function cannotWithdrawWithoutExitingBalance(): void
    {
        $amount = 20.00;
        $user = User::factory()->create();

        $this->expectException(BalanceNotFoundException::class);
        $this->operator->withdraw($user->id, $amount);
    }

    #[Test]
    public function cannotWithdrawWithInsufficientFunds(): void
    {
        $startBalance = 100.00;
        $amount = 200.00;
        $user = User::factory()->create();
        Balance::factory()->for($user)->withAmount($startBalance)->create();

        $this->expectException(InsufficientFundsException::class);
        $this->operator->withdraw($user->id, $amount);
    }

    #[Test]
    public function canTransfer(): void
    {
        $senderStartBalance = 100.00;
        $sender = User::factory()->create();
        Balance::factory()->for($sender)->withAmount($senderStartBalance)->create();
        $recipientStartBalance = 10.00;
        $recipient = User::factory()->create();
        Balance::factory()->for($recipient)->withAmount($recipientStartBalance)->create();
        $transferAmount = 50.00;

        $this->operator->transfer($sender->id, $recipient->id, $transferAmount);

        $this->assertDatabaseHas('balances', [
            'user_id' => $sender->id,
            'amount' => $senderStartBalance - $transferAmount,
        ]);

        $this->assertDatabaseHas('balances', [
            'user_id' => $recipient->id,
            'amount' => $recipientStartBalance + $transferAmount,
        ]);
    }

    #[Test]
    public function cannotTransferYourself(): void
    {
        $sender = User::factory()->create();

        $this->expectException(TransferringToYourselfException::class);
        $this->operator->transfer($sender->id, $sender->id, 100.00);
    }

    #[Test]
    public function cannotTransferIfSenderBalanceNotExists(): void
    {
        $sender = User::factory()->create();
        $recipientStartBalance = 10.00;
        $recipient = User::factory()->create();
        Balance::factory()->for($recipient)->withAmount($recipientStartBalance)->create();
        $transferAmount = 50.00;

        $this->expectException(BalanceNotFoundException::class);
        $this->operator->transfer($sender->id, $recipient->id, $transferAmount);
    }

    #[Test]
    public function canTransferIfRecipientBalanceNotExists(): void
    {
        $senderStartBalance = 100.00;
        $sender = User::factory()->create();
        Balance::factory()->for($sender)->withAmount($senderStartBalance)->create();
        $recipient = User::factory()->create();
        $transferAmount = 50.00;

        $this->operator->transfer($sender->id, $recipient->id, $transferAmount);

        $this->assertDatabaseHas('balances', [
            'user_id' => $sender->id,
            'amount' => $senderStartBalance - $transferAmount,
        ]);

        $this->assertDatabaseHas('balances', [
            'user_id' => $recipient->id,
            'amount' => $transferAmount,
        ]);
    }

    #[Test]
    public function cannotTransferIfSenderWithInsufficientFunds(): void
    {
        $senderStartBalance = 100.00;
        $sender = User::factory()->create();
        Balance::factory()->for($sender)->withAmount($senderStartBalance)->create();
        $recipientStartBalance = 10.00;
        $recipient = User::factory()->create();
        Balance::factory()->for($recipient)->withAmount($recipientStartBalance)->create();
        $transferAmount = 500.00;

        $this->expectException(InsufficientFundsException::class);
        $this->operator->transfer($sender->id, $recipient->id, $transferAmount);
    }
}
