<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;


use App\Exceptions\LowBalanceException;
use App\Exceptions\NotWalletOwnerException;
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
     * @OA\Post(
     *      path="/api/v1/transactions",
     *      operationId="sendTransaction",
     *      tags={"Transaction"},
     *      summary="Send transaction between wallets",
     *      description="Returns success answer",
     *
     *     @OA\Parameter(
     *          name="from_wallet_id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *
     *     @OA\Parameter(
     *          name="to_wallet_id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *
     *     @OA\Parameter(
     *          name="amount",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="numeric"
     *          )
     *     ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *
     *      @OA\Response(
     *          response=422,
     *          description="Bad Request"
     *      ),
     *
     *      @OA\Response(
     *          response=404,
     *          description="not found"
     *      ),
     *  )
     *
     * @param CreateTransactionRequest $request
     * @param TransactionService $transactionService
     * @param MoneyService $moneyService
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(
        CreateTransactionRequest $request,
        TransactionService $transactionService,
        MoneyService $moneyService
    ): JsonResponse
    {
        /** @var  CreateTransactionRequest $fromUser */
        $fromUser = $request->user();
        $fromWalletId = (int)$request->post('from_wallet_id');
        $toWalletId = (int)$request->post('to_wallet_id');
        $amount = $request->post('amount');
        $amount = $moneyService->convertToSatoshi($amount);

        try {
            $transactionService->send($fromUser->id, $fromWalletId, $toWalletId, $amount);
        } catch (NotWalletOwnerException $e) {
            return $this->errorResponse([$e->getMessage()]);
        } catch (LowBalanceException $e) {
            return $this->errorResponse([$e->getMessage()]);
        } catch (\DomainException $e) {
            return $this->errorResponse([$e->getMessage()]);
        } catch (Exception $e) {
            return $this->errorResponse([$e->getMessage()]);
        }

        return $this->successResponse(['success' => 'Transaction success']);
    }
}
