<?php

declare(strict_types = 1);

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateTransactionRequest
 * @package App\Http\Requests
 */
class CreateTransactionRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'to_user_id' => 'required|integer|exists:users,id',
            'from_wallet_address' => 'required|string|exists:wallets,address',
            'to_wallet_address' => 'required|string|exists:wallets,address',
            'amount' => 'required|numeric',
        ];
    }
}
