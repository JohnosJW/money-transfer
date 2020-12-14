<?php

declare(strict_types = 1);

namespace Tests\Unit;


use App\Models\User;
use App\Models\Wallet;
use App\Services\MoneyService;
use Illuminate\Database\DatabaseManager;
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

    /** @var object  */
    protected object $walletRepository;

    /** @var object  */
    protected object $transactionService;

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->walletRepository = $this->app->make('App\Repositories\Interfaces\WalletRepositoryInterface');
        $this->transactionService = $this->app->make('App\Services\TransactionService');
    }

    /**
     * @test
     */
    public function performTransaction()
    {
        $userFrom = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => bcrypt($this->faker->password),
        ]);

        $userTo = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => bcrypt($this->faker->password),
        ]);

        $walletFrom = Wallet::create([
            'user_id' => $userFrom->id,
        ]);

        $walletTo = Wallet::create([
            'user_id' => $userTo->id,
        ]);

        $userFromWallet = $this->walletRepository
            ->getByUserIdAndAddressWithLockForUpdate($userFrom->id, $walletFrom->address)->first();

        $userToWallet = $this->walletRepository
            ->getByUserIdAndAddressWithLockForUpdate($userTo->id, $walletTo->address)->first();

        $amount = MoneyService::convertToSatoshi("0.009876");
        $amountWithCommission = MoneyService::getAmountWithCommission($amount);

        app(DatabaseManager::class)->beginTransaction();
        $result = $this->transactionService->performTransaction($userFromWallet, $userToWallet, $amount, $amountWithCommission);
        app(DatabaseManager::class)->commit();

        self::assertTrue($result);
    }

    /**
     * @test
     */
    public function sendTransaction()
    {
        $userFrom = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => bcrypt($this->faker->password),
        ]);

        $userTo = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => bcrypt($this->faker->password),
        ]);

        $walletFrom = Wallet::create([
            'user_id' => $userFrom->id,
        ]);

        $walletTo = Wallet::create([
            'user_id' => $userTo->id,
        ]);

        $amount = MoneyService::convertToSatoshi("0.009876");

        $result = $this->transactionService->send($userFrom->id, $userTo->id, $walletFrom->address, $walletTo->address, $amount);

        self::assertTrue($result);
    }
}
