<?php

declare(strict_types = 1);

namespace Tests\Controller\Api\V1;


use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class TransactionControllerTest
 * @package Tests\Controler\Api\V1
 */
class TransactionControllerTest extends TestCase
{
    /** @test */
    public function testCreatePositiveScenario(): void
    {
        /** @var  $amount */
        $amount = 10;

        $userFrom = User::create([
            'name' => 'User' . rand(100, 100000),
            'email' => 'user' . rand() . '@app.app',
            'password' => bcrypt('123456'),
        ]);

        $userTo = User::create([
            'name' => 'User' . rand(100, 100000),
            'email' => 'user' . rand() . '@app.app',
            'password' => bcrypt('123456'),
        ]);

        /** @var  $walletOfFirstUser */
        $walletFrom = Wallet::create([
            'user_id' => $userFrom->id,
        ]);

        /** @var  $walletOfFirstUser */
        $walletTo = Wallet::create([
            'user_id' => $userTo->id,
        ]);

        Passport::actingAs(
            $userFrom,
            ['create-servers']
        );

        $response = $this->postJson('/api/v1/transactions', [
            "from_user_id" => $userFrom->id,
            "to_user_id" => $userTo->id,
            "from_wallet_address" => $walletFrom->address,
            "to_wallet_address" => $walletTo->address,
            "amount" => $amount,
        ]);

        $response->assertStatus(200);

        $response->assertJson(['data' => [
            "userFromWallet" => [
                "id" => $walletFrom->id,
                "user_id" => $walletFrom->user_id,
                "address" => $walletFrom->address,
            ],
            "userToWallet" => [
                "id" => $walletTo->id,
                "user_id" => $walletTo->user_id,
                "address" => $walletTo->address,
            ],
            "debitTransaction" => [
                "user_id" => $userTo->id,
                "type" => Transaction::TYPE_DEBIT,
                "from_wallet_address" => $walletFrom->address,
                "to_wallet_address" => $walletTo->address,
                "amount" => $amount * Wallet::CENTS_IN_ONE_CURRENCY,
                "status" => Transaction::STATUS_PENDING
            ],
            "creditTransaction" => [
                "user_id" => $userFrom->id,
                "type" => Transaction::TYPE_CREDIT,
                "from_wallet_address" => $walletFrom->address,
                "to_wallet_address" => $walletTo->address,
                "amount" => $amount * Wallet::CENTS_IN_ONE_CURRENCY,
                "status" => Transaction::STATUS_PENDING,
            ],
        ]]);
    }

    /** @test */
    public function testCreateNegativeScenarioLowBalance(): void
    {
        /** @var  $amount */
        $amount = 1000;

        /** @var  $message */
        $message = "Too low balance";

        $userFrom = User::create([
            'name' => 'User' . rand(100, 100000),
            'email' => 'user' . rand() . '@app.app',
            'password' => bcrypt('123456'),
        ]);

        $userTo = User::create([
            'name' => 'User' . rand(100, 100000),
            'email' => 'user' . rand() . '@app.app',
            'password' => bcrypt('123456'),
        ]);

        /** @var  $walletOfFirstUser */
        $walletFrom = Wallet::create([
            'user_id' => $userFrom->id,
        ]);

        /** @var  $walletOfFirstUser */
        $walletTo = Wallet::create([
            'user_id' => $userTo->id,
        ]);

        Passport::actingAs(
            $userFrom,
            ['create-servers']
        );

        $response = $this->postJson('/api/v1/transactions', [
            "from_user_id" => $userFrom->id,
            "to_user_id" => $userTo->id,
            "from_wallet_address" => $walletFrom->address,
            "to_wallet_address" => $walletTo->address,
            "amount" => $amount,
        ]);

        $response->assertStatus(500);

        $this->assertEquals($message, $response->exception->getMessage());
    }
}
