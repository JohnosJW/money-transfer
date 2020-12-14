<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Api\ApiBaseController;
use App\Http\Requests\CreateTransactionRequest;
use App\Services\MoneyService;
use App\Services\TransactionService;
use Exception;
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
        /** @var  CreateTransactionRequest $fromUser */
        $fromUser = $request->user();
        $toUserId = (int)$request->post('to_user_id');
        $addressFrom = $request->post('from_wallet_address');
        $addressTo = $request->post('to_wallet_address');
        $amount = $request->post('amount');
        $amount = MoneyService::convertToSatoshi($amount);

        try {
            $transactionService->send($fromUser->id, $toUserId, $addressFrom, $addressTo, $amount);
        } catch (\DomainException $e) {
            return $this->errorResponse([$e->getMessage()]);
        } catch (Exception $e) {
            return  $this->errorResponse([$e->getMessage()]);
        }

        return $this->successResponse(['success' => 'Transaction success']);
    }
}
