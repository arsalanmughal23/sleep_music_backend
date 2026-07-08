<?php

namespace App\Http\Requests\Admin;
use App\Rules\StrongPassword;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ResetPasswordForWebRequest
 * @package App\Http\Requests\Api
 */
class ResetPasswordForWebRequest extends FormRequest
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
            'email'             => 'required|email|exists:password_resets,email',
            'password'          => ['required', 'min:8', 'required_with:password_confirmation', 'same:password_confirmation', new StrongPassword],
            'password_confirmation' => 'required',
        ];
    }

}
