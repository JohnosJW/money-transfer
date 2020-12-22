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
     * @param int $id
     * @return mixed
     */
    public function getByIdWithLockForUpdate(int $id);
}
