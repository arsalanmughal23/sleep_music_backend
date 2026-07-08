<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use App\Rules\StrongPassword;

class CreateUserRequest extends FormRequest
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
        // return User::$rules;
        return [
            'name'                  => 'required|max:300',
            'first_name'            => 'required',
            'last_name'             => 'required',
            'email'                 => 'required|max:320|email|unique:users,email,NULL,id,deleted_at,NULL',
            'password'              => ['required', 'min:8', 'required_with:password_confirmation', 'same:password_confirmation', new StrongPassword],
            'password_confirmation' => 'required|min:8',
            'roles'                 => 'required|exists:roles,id',
        ];
    }
}
