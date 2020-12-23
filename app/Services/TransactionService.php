<?php

declare(strict_types=1);

namespace App\Services;


use App\Enums\TransactionType;
use App\Exceptions\LowBalanceException;
use App\Exceptions\NotWalletOwnerException;
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
    /** @var WalletRepositoryInterface */
    public WalletRepositoryInterface $walletRepository;

    /** @var DatabaseManager */
    public DatabaseManager $databaseManager;

    /** @var MoneyService  */
    public MoneyService $moneyService;

    /**
     * TransactionService constructor.
     * @param DatabaseManager $databaseManager
     * @param WalletRepositoryInterface $walletRepository
     * @param MoneyService $moneyService
     */
    public function __construct(
        DatabaseManager $databaseManager,
        WalletRepositoryInterface $walletRepository,
        MoneyService $moneyService
    )
    {
        $this->walletRepository = $walletRepository;
        $this->databaseManager = $databaseManager;
        $this->moneyService = $moneyService;
    }

    /**
     * @param int $fromUserId
     * @param int $fromWalletId
     * @param int $toWalletId
     * @param int $amount
     * @return bool
     * @throws LowBalanceException
     * @throws NotWalletOwnerException
     * @throws Throwable
     */
    public function send(int $fromUserId, int $fromWalletId, int $toWalletId, int $amount): bool
    {
        $userFromWallet = $this->walletRepository
            ->getByIdWithLockForUpdate($fromWalletId);

        $userToWallet = $this->walletRepository
            ->getByIdWithLockForUpdate($toWalletId);

        $amountWithCommission = $this->moneyService->getAmountWithCommission((string)$amount);

        if (!$userFromWallet || $userFromWallet->user_id !== $fromUserId) {
            throw new NotWalletOwnerException('You are not owner of this wallet');
        }

        if ($userFromWallet->balance < $amountWithCommission) {
            throw new LowBalanceException('Too low balance');
        }

        try {
            $this->databaseManager->beginTransaction();
            $result = $this->performTransaction($userFromWallet, $userToWallet, $amount, $amountWithCommission);
            $this->databaseManager->commit();
        } catch (Throwable $exception) {
            $this->databaseManager->rollBack();
            throw $exception;
        }

        return $result;
    }

    /**
     * @param Wallet $userFromWallet
     * @param Wallet $userToWallet
     * @param int $amount
     * @param int $amountWithCommission
     * @return bool
     */
    public function performTransaction(Wallet $userFromWallet, Wallet $userToWallet, int $amount, int $amountWithCommission): bool
    {
        $debitTransaction = new Transaction();
        $debitTransaction->user_id = $userToWallet->user_id;
        $debitTransaction->type = TransactionType::TYPE_DEBIT;
        $debitTransaction->from_wallet_id = $userFromWallet->id;
        $debitTransaction->to_wallet_id = $userToWallet->id;
        $debitTransaction->amount = $amount;

        $creditTransaction = new Transaction();
        $creditTransaction->user_id = $userFromWallet->user_id;
        $creditTransaction->type = TransactionType::TYPE_CREDIT;
        $creditTransaction->from_wallet_id = $userFromWallet->id;
        $creditTransaction->to_wallet_id = $userToWallet->id;
        $creditTransaction->amount = $amount;
        $creditTransaction->commission = $amountWithCommission - $amount;

        $userFromWallet->balance -= $amountWithCommission;
        $userToWallet->balance += $amount;

        $debitTransaction->save();
        $creditTransaction->save();

        $userFromWallet->save();
        $userToWallet->save();

        return true;
    }
}
