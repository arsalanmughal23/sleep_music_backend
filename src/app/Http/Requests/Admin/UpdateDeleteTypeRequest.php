<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\DeleteType;

class UpdateDeleteTypeRequest extends FormRequest
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
        // return DeleteType::$update_rules;
        $deleteTypeId = $this->route('delete_type');

        return [
            'name'   => 'required|max:180|unique:delete_types,name,'.$deleteTypeId.',id,deleted_at,NULL',
            'status' => 'required'
        ];
    }
}
