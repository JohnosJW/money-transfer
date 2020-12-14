<?php

declare(strict_types=1);

namespace App\Services;


use App\Models\Transaction;
use App\Models\Wallet;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use Illuminate\Database\DatabaseManager;
use Throwable;

/**
 * Class TransactionService
 * @package App\Services
 */
class TransactionService
{
    /**
     * @var WalletRepositoryInterface
     */
    public $walletRepository;

    /**
     * TransactionService constructor.
     * @param WalletRepositoryInterface $walletRepository
     */
    public function __construct(WalletRepositoryInterface $walletRepository)
    {
        $this->walletRepository = $walletRepository;
    }

    /**
     * @param int $fromUserId
     * @param int $toUserId
     * @param string $addressFrom
     * @param string $addressTo
     * @param string $amount
     * @return bool
     * @throws Throwable
     */
    public function send(int $fromUserId, int $toUserId, string $addressFrom, string $addressTo, string $amount): bool
    {
        $userFromWallet = $this->walletRepository
            ->getByUserIdAndAddressWithLockForUpdate($fromUserId, $addressFrom)->first();

        $userToWallet = $this->walletRepository
            ->getByUserIdAndAddressWithLockForUpdate($toUserId, $addressTo)->first();

        $amountWithCommission = MoneyService::getAmountWithCommission($amount);

        if (empty($userFromWallet->user_id) || $userFromWallet->user_id !== $fromUserId) {
            throw new \RuntimeException('Not correct from address', 422);
        }

        if (empty($userToWallet->user_id) || $userToWallet->user_id !== $toUserId) {
            throw new \RuntimeException('Not correct to address', 422);
        }

        if ($userFromWallet->balance < $amountWithCommission) {
            throw new \RuntimeException('Too low balance', 422);
        }

        if ($amount <= 0) {
            throw new \RuntimeException('Not correct amount', 422);
        }

        try {
            app(DatabaseManager::class)->beginTransaction();
            $this->performTransaction($userFromWallet, $userToWallet, $amount, $amountWithCommission);
            app(DatabaseManager::class)->commit();
        } catch (Throwable $exception) {
            app(DatabaseManager::class)->rollBack();
            throw $exception;
        }

        return true;
    }

    /**
     * @param Wallet $userFromWallet
     * @param Wallet $userToWallet
     * @param string $amount
     * @param string $amountWithCommission
     * @return bool
     */
    public function performTransaction(Wallet $userFromWallet, Wallet $userToWallet, string $amount, string $amountWithCommission): bool
    {
        $debitTransaction = new Transaction();
        $debitTransaction->user_id = $userToWallet->user_id;
        $debitTransaction->type = Transaction::TYPE_DEBIT;
        $debitTransaction->from_wallet_address = $userFromWallet->address;
        $debitTransaction->to_wallet_address = $userToWallet->address;
        $debitTransaction->amount = $amount;
        $debitTransaction->status = Transaction::STATUS_DONE;

        $creditTransaction = new Transaction();
        $creditTransaction->user_id = $userFromWallet->user_id;
        $creditTransaction->type = Transaction::TYPE_CREDIT;
        $creditTransaction->from_wallet_address = $userFromWallet->address;
        $creditTransaction->to_wallet_address = $userToWallet->address;
        $creditTransaction->amount = $amount;
        $creditTransaction->commission = $amountWithCommission - $amount;
        $creditTransaction->status = Transaction::STATUS_DONE;

        $userFromWallet->balance -= $amountWithCommission;
        $userToWallet->balance += $amount;

        $debitTransaction->save();
        $creditTransaction->save();

        $userFromWallet->save();
        $userToWallet->save();

        return true;
    }
}
