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
     * @return string
     */
    public static function convertToSatoshi(string $amount): string
    {
        return bcmul((string)$amount, (string)Wallet::SATOSHI_IN_ONE_BITCOIN, 2);
    }

    /**
     * @param string $amount
     * @return string
     */
    public static function getAmountWithCommission(string $amount): string
    {
        return bcadd($amount, bcdiv(bcmul(env('COMMISSION'), $amount, 2), '100', 2));
    }
}
