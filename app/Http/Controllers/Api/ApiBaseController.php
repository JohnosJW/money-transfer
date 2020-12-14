<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiBaseController
 * @package App\Http\Controllers\Api
 */
class ApiBaseController extends Controller
{
    /**
     * @param array $data
     * @param int $responseCode
     * @return JsonResponse
     */
    protected function successResponse(array $data = [], $responseCode = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse($data, $responseCode);
    }

    /**
     * @param array $data
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function errorResponse(array $data = [], $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        return new JsonResponse($data, $statusCode);
    }
}
