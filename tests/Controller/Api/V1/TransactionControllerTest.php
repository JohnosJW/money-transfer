<?php

declare(strict_types = 1);

namespace Tests\Controller\Api\V1;


use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
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
            "to_user_id" => $userTo->id,
            "from_wallet_address" => $walletFrom->address,
            "to_wallet_address" => $walletTo->address,
            "amount" => $amount,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => 'Transaction success']);
    }

    /** @test */
    public function createNegativeScenarioLowBalance(): void
    {
        /** @var  $amount */
        $amount = "1000";

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
            "to_user_id" => $userTo->id,
            "from_wallet_address" => $walletFrom->address,
            "to_wallet_address" => $walletTo->address,
            "amount" => $amount,
        ]);

        $response->assertStatus(422);
        $response->assertJson(['Too low balance']);
    }
}
