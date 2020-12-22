<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Auth;


use App\Http\Controllers\Api\ApiBaseController;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class AuthController
 * @package App\Http\Controllers\Api\Auth
 */
class AuthController extends ApiBaseController
{
    /**
     * @OA\Post(
     ** path="/api/v1/auth",
     *   tags={"Login"},
     *   summary="Login",
     *   operationId="login",
     *
     *   @OA\Parameter(
     *      name="email",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *           type="string"
     *      )
     *   ),
     *   @OA\Parameter(
     *      name="password",
     *      in="query",
     *      required=true,
     *      @OA\Schema(
     *          type="string"
     *      )
     *   ),
     *   @OA\Response(
     *      response=200,
     *       description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=401,
     *       description="Unauthenticated"
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        /** @var  $loginData */
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required',
        ]);

        if (!auth()->attempt($loginData)) {
            return $this->errorResponse(['message' => 'Invalid credentials']);
        }

        /** @var  $accessToken */
        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return $this->successResponse([
            'user' => auth()->user(),
            'access_token' => $accessToken
        ]);
    }
}
