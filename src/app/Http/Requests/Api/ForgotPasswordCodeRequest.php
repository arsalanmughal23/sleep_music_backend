<?php

namespace App\Http\Requests\Api;

/**
 * Class ForgotPasswordCodeRequest
 * @package App\Http\Requests\Api
 */
class ForgotPasswordCodeRequest extends BaseAPIRequest
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
            'email' => 'required|exists:users,email',
            'type' => 'nullable|in:email,password'
        ];
    }

    public function messages()
    {
        return [
            'email.exists' => 'Email is not registered'
        ];
    }
}
