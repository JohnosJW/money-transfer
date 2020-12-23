<?php

declare(strict_types = 1);

namespace Tests\Unit;


use App\Models\User;
use App\Models\Wallet;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use App\Services\MoneyService;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

/**
 * Class TransactionTest
 * @package Tests\Unit
 */
class TransactionTest extends TestCase
{
    use WithoutMiddleware, WithFaker;

    /** @var WalletRepositoryInterface  */
    protected WalletRepositoryInterface $walletRepository;

    /** @var TransactionService  */
    protected TransactionService $transactionService;

    /** @var MoneyService  */
    protected MoneyService $moneyService;

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->walletRepository = $this->app->make(WalletRepositoryInterface::class);
        $this->transactionService = $this->app->make(TransactionService::class);
        $this->moneyService = $this->app->make(MoneyService::class);
    }

    /** @test */
    public function performTransaction()
    {
        $userFrom = User::factory()->create();
        $userTo = User::factory()->create();

        $walletFrom = Wallet::create([
            'user_id' => $userFrom->id,
        ]);

        $walletTo = Wallet::create([
            'user_id' => $userTo->id,
        ]);

        $userFromWallet = $this->walletRepository
            ->getByIdWithLockForUpdate($walletFrom->id);

        $userToWallet = $this->walletRepository
            ->getByIdWithLockForUpdate($walletTo->id);

        $amount = $this->moneyService->convertToSatoshi("0.009876");
        $amountWithCommission = $this->moneyService->getAmountWithCommission((string)$amount);

        $result = $this->transactionService->performTransaction($userFromWallet, $userToWallet, $amount, $amountWithCommission);

        self::assertTrue($result);
    }

    /** @test */
    public function sendTransaction()
    {
        $userFrom = User::factory()->create();
        $userTo = User::factory()->create();

        $walletFrom = Wallet::create([
            'user_id' => $userFrom->id,
        ]);

        $walletTo = Wallet::create([
            'user_id' => $userTo->id,
        ]);

        $amount = $this->moneyService->convertToSatoshi("0.009876");

        $result = $this->transactionService->send($userFrom->id, $walletFrom->id, $walletTo->id, $amount);

        self::assertTrue($result);
    }
}
