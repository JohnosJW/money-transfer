<?php


namespace App\Services;


use App\Models\Transaction;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use Illuminate\Support\Facades\DB;
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
     * @param int $amount
     * @return array
     * @throws Throwable
     */
    public function send(int $fromUserId, int $toUserId, string $addressFrom, string $addressTo, int $amount): array
    {
        /** @var  $userFromWallet */
        $userFromWallet = $this->walletRepository
            ->getByUserIdAndAddressWithLockForUpdate($fromUserId, $addressFrom)->first();

        /** @var  $userToWallet */
        $userToWallet = $this->walletRepository
            ->getByUserIdAndAddressWithLockForUpdate($toUserId, $addressTo)->first();

        /** @var  $amountWithCommission */
        $amountWithCommission = round($amount * Transaction::COMMISSION, 0);

        if (empty($userFromWallet->user_id) || $userFromWallet->user_id !== $fromUserId) {
            throw new \RuntimeException('Not correct from address');
        }

        if (empty($userToWallet->user_id) || $userToWallet->user_id !== $toUserId) {
            throw new \RuntimeException('Not correct to address');
        }

        if ($userFromWallet->balance < $amountWithCommission) {
            throw new \RuntimeException('Too low balance');
        }

        if (!$amount > 0) {
            throw new \RuntimeException('Not correct amount');
        }

        try {
            DB::beginTransaction();

            $data = $this->performTransaction($userFromWallet, $userToWallet, $amount, $amountWithCommission);

            DB::commit();

            return $data;
        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    /**
     * @param $userFromWallet
     * @param $userToWallet
     * @param int $amount
     * @param int $amountWithCommission
     * @return array
     */
    public function performTransaction($userFromWallet, $userToWallet, int $amount, int $amountWithCommission): array
    {
        /** @var  $debitTransaction */
        $debitTransaction = new Transaction();
        $debitTransaction->user_id = $userToWallet->user_id;
        $debitTransaction->type = Transaction::TYPE_DEBIT;
        $debitTransaction->from_wallet_address = $userFromWallet->address;
        $debitTransaction->to_wallet_address = $userToWallet->address;
        $debitTransaction->amount = $amount;
        $debitTransaction->status = Transaction::STATUS_PENDING;

        /** @var  $creditTransaction */
        $creditTransaction = new Transaction();
        $creditTransaction->user_id = $userFromWallet->user_id;
        $creditTransaction->type = Transaction::TYPE_CREDIT;
        $creditTransaction->from_wallet_address = $userFromWallet->address;
        $creditTransaction->to_wallet_address = $userToWallet->address;
        $creditTransaction->amount = $amount;
        $creditTransaction->commission = $amountWithCommission - $amount;
        $creditTransaction->status = Transaction::STATUS_PENDING;

        $userFromWallet->balance -= $amountWithCommission;
        $userToWallet->balance += $amount;

        $debitTransaction->save();
        $creditTransaction->save();

        $userFromWallet->save();
        $userToWallet->save();

        return [
            'userFromWallet' => $userFromWallet,
            'userToWallet' => $userToWallet,
            'debitTransaction' => $debitTransaction,
            'creditTransaction' => $creditTransaction,
        ];
    }
}
