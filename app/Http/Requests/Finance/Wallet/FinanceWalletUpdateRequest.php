<?php

namespace App\Http\Requests\Finance\Wallet;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FinanceWalletUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'description'   => 'required|string',
            'json'          => 'nullable|string',
            'enable'        => 'required|integer',
            'panel'         => 'required|integer',
        ];
    }
}
