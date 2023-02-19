<?php

namespace App\Http\Requests\Finance\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FinanceCategoryStoreRequest extends FormRequest
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
            'enable'        => 'required|integer',
            'obs'           => 'required|string',
            'wallet_id'     => 'required|integer',
            // 'type_id'       => 'nullable|integer',
            'group_id'      => 'nullable|integer',
        ];
    }
}
