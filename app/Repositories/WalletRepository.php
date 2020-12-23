<?php

declare(strict_types = 1);

namespace App\Repositories;


use App\Models\Wallet;
use App\Repositories\Interfaces\WalletRepositoryInterface;

/**
 * Class WalletRepository
 * @package App\Repositories
 */
class WalletRepository implements WalletRepositoryInterface
{
    /**
     * @param int $id
     * @return mixed
     */
    public function getByIdWithLockForUpdate(int $id)
    {
        return Wallet::lockForUpdate()->find($id);
    }
}
