<?php

declare(strict_types = 1);

namespace App\Repositories\Interfaces;


/**
 * Interface WalletRepositoryInterface
 * @package App\Repositories\Interfaces
 */
interface WalletRepositoryInterface
{
    /**
     * @param int $userId
     * @param string $address
     * @return mixed
     */
    public function getByUserIdAndAddressWithLockForUpdate(int $userId, string $address);
}
