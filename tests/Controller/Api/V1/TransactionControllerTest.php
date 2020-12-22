<?php

declare(strict_types = 1);

namespace Tests\Controller\Api\V1;


use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * Class TransactionControllerTest
 * @package Tests\Controler\Api\V1
 */
class TransactionControllerTest extends TestCase
{
    use WithFaker;

    /** @test */
    public function createPositiveScenario(): void
    {
        $amount = "0.001";
        $userFrom = User::factory()->create();
        $userTo = User::factory()->create();

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

        $response = $this->postJson(route('transactions'), [
            "from_wallet_id" => $walletFrom->id,
            "to_wallet_id" => $walletTo->id,
            "amount" => $amount,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(['success' => 'Transaction success']);
    }

    /** @test */
    public function createNegativeScenarioLowBalance(): void
    {
        /** @var  $amount */
        $amount = "1000";
        $userFrom = User::factory()->create();
        $userTo = User::factory()->create();

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

        $response = $this->postJson(route('transactions'), [
            "from_wallet_id" => $walletFrom->id,
            "to_wallet_id" => $walletTo->id,
            "amount" => $amount,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson(['Too low balance']);
    }

    /** @test */
    public function createNegativeScenarioNotOwnerWallet(): void
    {
        /** @var  $amount */
        $amount = "1000";
        $userFrom = User::factory()->create();
        $userTo = User::factory()->create();

        /** @var  $walletOfFirstUser */
        $walletTo = Wallet::create([
            'user_id' => $userTo->id,
        ]);

        Passport::actingAs(
            $userFrom,
            ['create-servers']
        );

        $response = $this->postJson(route('transactions'), [
            "from_wallet_id" => $walletTo->id,
            "to_wallet_id" => $walletTo->id,
            "amount" => $amount,
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson(['You are not owner of this wallet']);
    }
}
