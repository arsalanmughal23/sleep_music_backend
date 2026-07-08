<?php

namespace App\Http\Requests\Api;

use App\Models\Register;
use App\Rules\StrongPassword;

/**
 * Class RegistrationAPIRequest
 * @package App\Http\Requests\Api
 */
class RegistrationAPIRequest extends BaseAPIRequest
{
    /**
     * Determine if the user is authorized to make this registration.
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

    // public function rules()
    // {
    //     return Register::$rules;
    // }

    public function rules()
    {
        return [
            'name'                  => 'required|max:300',
            'first_name'            => 'required|max:180',
            'last_name'             => 'required|max:180',
            'email'                 => 'required|max:320|email|unique:users,email,NULL,id,deleted_at,NULL',
            'password'              => ['required', 'min:8', 'required_with:password_confirmation', 'same:password_confirmation', new StrongPassword],
            'password_confirmation' => 'required',
            'device_token'          => 'sometimes|required',
            'device_type'           => 'required|string|in:ios,android,web',
            'image'                 => 'sometimes'
        ];
    }
}

