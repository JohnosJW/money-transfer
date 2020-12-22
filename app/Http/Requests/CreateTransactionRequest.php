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
            'from_wallet_id' => 'required|integer|exists:wallets,id',
            'to_wallet_id' => 'required|integer|exists:wallets,id',
            'amount' => 'required|numeric',
        ];
    }
}
