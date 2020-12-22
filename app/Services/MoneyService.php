<?php

declare(strict_types = 1);

namespace App\Services;


use App\Models\Wallet;

/**
 * Class MoneyService
 * @package App\Services
 */
class MoneyService
{
    /**
     * @param string $amount
     * @return int
     */
    public function convertToSatoshi(string $amount): int
    {
        return (int)bcmul($amount, (string)Wallet::SATOSHI_IN_ONE_BITCOIN);
    }

    /**
     * @param string $amount
     * @return int
     */
    public function getAmountWithCommission(string $amount): int
    {
        return (int)bcadd($amount, bcdiv(bcmul(config('app.commission'), $amount, 2), '100'));
    }
}
