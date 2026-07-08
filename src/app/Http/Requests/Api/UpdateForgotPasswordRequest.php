<?php

namespace App\Http\Requests\Api;
use App\Rules\StrongPassword;

/**
 * Class UpdateForgotPasswordRequest
 * @package App\Http\Requests\Api
 */
class UpdateForgotPasswordRequest extends BaseAPIRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'verification_code' => 'required',
            'email'             => 'required|email|exists:password_resets,email',
            'password'          => ['required', 'min:8', 'required_with:password_confirmation', 'same:password_confirmation', new StrongPassword],
            'password_confirmation' => 'required',
        ];
    }

}
