<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreatePaypalOrderAPIRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'datetime'      => 'required',
            'total_amount'  => 'required',
            'card_id'       => 'sometimes',
            'donated_to_id' => 'required',
            'text'          => 'required|max:150',
            'nonce'         => 'required',
        ];
    }
}
