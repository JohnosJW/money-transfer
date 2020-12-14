<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Requests\CreateTransactionRequest;
use App\Models\Wallet;
use App\Services\TransactionService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;

/**
 * Class TransactionController
 * @package App\Http\Controllers\Api\V1
 */
class TransactionController extends ApiBaseController
{
    /**
     * @param CreateTransactionRequest $request
     * @param TransactionService $transactionService
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(CreateTransactionRequest $request, TransactionService $transactionService): JsonResponse
    {
        /** @var  $fromUser */
        $fromUser = $request->user();

        /** @var  $toUserId */
        $toUserId = (int)$request->post('to_user_id');

        /** @var  $address */
        $addressFrom = $request->post('from_wallet_address');

        /** @var  $addressTo */
        $addressTo = $request->post('to_wallet_address');

        /** @var  $amount */
        $amount = $request->post('amount');

        // convert currency in a little coins (cents)
        $amount = $amount * Wallet::CENTS_IN_ONE_CURRENCY;

        try {
            $data = $transactionService->send($fromUser->id, $toUserId, $addressFrom, $addressTo, $amount);

            return $this->successResponse([
                'data' => $data,
            ]);
        } catch (\DomainException $e) {
            return $this->errorResponse([$e->getMessage()]);
        }
    }
}
