<?php

namespace Services;

use App\DTO\BalanceRequestDto;
use App\DTO\BalanceResultDto;
use App\DTO\DepositDto;
use App\DTO\TransferDTO;
use App\DTO\WithdrawDTO;
use App\Models\User;
use App\Services\BalanceOperator;
use App\Services\BalanceService;
use App\Services\TransactionRegistrar;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BalanceServiceTest extends TestCase
{
    use RefreshDatabase;

    private BalanceService $service;
    private BalanceOperator $operator;
    private TransactionRegistrar $registrar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->operator = $this->createMock(BalanceOperator::class);
        $this->registrar = $this->createMock(TransactionRegistrar::class);
        $this->service = new BalanceService($this->registrar, $this->operator);
    }

    #[Test]
    public function canGetBalance(): void
    {
        $user = User::factory()->create();
        $this->operator->method('getBalance')->willReturn(100.50);

        $result = $this->service->getBalance(new BalanceRequestDto($user->id));

        $this->assertInstanceOf(BalanceResultDto::class, $result);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals(100.50, $result->balance);
    }

    #[Test]
    public function canDeposit(): void
    {
        $user = User::factory()->create();
        $dto = new DepositDto($user->id, 100, 'Пополнение');

        $this->operator->expects($this->once())->method('deposit');
        $this->registrar->expects($this->once())->method('registerDepositOperation');

        $this->service->deposit($dto);
    }

    #[Test]
    public function cannotDepositIfUserNotExists(): void
    {
        $dto = new DepositDto(999, 100, 'Пополнение');

        $this->expectException(ModelNotFoundException::class);

        $this->service->deposit($dto);
    }

    #[Test]
    public function depositUsesDatabaseTransaction(): void
    {
        $user = User::factory()->create();
        $dto = new DepositDto($user->id, 100, 'Пополнение');

        DB::shouldReceive('transaction')->once();

        $this->service->deposit($dto);
    }

    #[Test]
    public function canWithdraw(): void
    {
        $user = User::factory()->create();
        $dto = new WithdrawDTO($user->id, 50, 'Списание');

        $this->operator->expects($this->once())->method('withdraw');
        $this->registrar->expects($this->once())->method('registerWithdrawOperation');

        $this->service->withdraw($dto);
    }

    #[Test]
    public function cannotWithdrawIfUserNotExists(): void
    {
        $dto = new WithdrawDTO(999, 50, 'Списание');

        $this->expectException(ModelNotFoundException::class);

        $this->service->withdraw($dto);
    }

    #[Test]
    public function withdrawUsesDatabaseTransaction(): void
    {
        $user = User::factory()->create();
        $dto = new WithdrawDTO($user->id, 50, 'Списание');

        DB::shouldReceive('transaction')->once();

        $this->service->withdraw($dto);
    }

    #[Test]
    public function canTransfer(): void
    {
        $fromUser = User::factory()->create();
        $toUser = User::factory()->create();
        $dto = new TransferDTO($fromUser->id, $toUser->id, 75, 'Перевод');

        $this->operator->expects($this->once())->method('transfer');
        $this->registrar->expects($this->once())->method('registerTransferOperation');

        $this->service->transfer($dto);
    }

    #[Test]
    public function cannotTransferIfUsersNotExist(): void
    {
        $dto = new TransferDTO(999, 888, 50, 'Перевод');

        $this->expectException(ModelNotFoundException::class);

        $this->service->transfer($dto);
    }

    #[Test]
    public function transferUsesDatabaseTransaction(): void
    {
        $fromUser = User::factory()->create();
        $toUser = User::factory()->create();
        $dto = new TransferDTO($fromUser->id, $toUser->id, 75, 'Перевод');

        DB::shouldReceive('transaction')->once();

        $this->service->transfer($dto);
    }
}
