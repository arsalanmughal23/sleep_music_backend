<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use App\Rules\StrongPassword;

class UpdateUserRequest extends FormRequest
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
        // return User::$update_rules;
        return [
            'name'                  => 'required|max:300',
            'first_name'            => 'required|max:180',
            'last_name'             => 'required|max:180',
            'image'                 => 'sometimes',
            'roles'                 => 'nullable|exists:roles,id,deleted_at,NULL',
            'password'              => ['nullable', 'required_with:password_confirmation', 'same:password_confirmation', new StrongPassword],
            'password_confirmation' => 'nullable|min:8'
        ];
    }
}
