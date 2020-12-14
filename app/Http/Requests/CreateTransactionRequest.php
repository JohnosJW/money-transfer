<?php


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
    public function authorize()
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function rules()
    {
        return [
            'from_user_id' => 'required|integer',
            'to_user_id' => 'required|integer',
            'from_wallet_address' => 'required|string',
            'to_wallet_address' => 'required|string',
            'amount' => 'required|integer',
        ];
    }
}
