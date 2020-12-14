<?php


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
     * @param int $userId
     * @param string $address
     * @return mixed
     */
    public function getByUserIdAndAddressWithLockForUpdate(int $userId, string $address)
    {
        return Wallet::where([
            'user_id' => $userId,
            'address' => $address
        ])
            ->lockForUpdate();
    }
}
