<?php

namespace App\Http\Requests\Finance\Item;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FinanceItemStoreRequest extends FormRequest
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
            "value"       => 'required|numeric',
            "date"        => 'required|string',
            "obs"         => 'required|string',
            "sort"        => 'required|integer',
            "enable"      => 'required|integer',
            "enable"      => 'required|integer',
            "repeat"      => ['required', Rule::in('UNIQUE', 'REPEAT')],
            "origin_id"   => 'required|exists:finance_origin,id',
            "status_id"   => 'required|exists:finance_status,id',
            "type_id"     => 'required|exists:finance_type,id',
            "tags_ids"    => 'required',
            // "category_id" => 'required|exists:finance_category,id',
            // "group_id"    => 'required|exists:finance_group,id',
            "wallet_id"   => 'required|exists:finance_wallet,id',
        ];
    }
}
