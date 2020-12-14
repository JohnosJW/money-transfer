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
