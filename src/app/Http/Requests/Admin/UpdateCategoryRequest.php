<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Category;

class UpdateCategoryRequest extends FormRequest
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
        // return Category::$update_rules;
        $categoryId = $this->route('category');
        
        return [
            'parent_id' => 'required',
            'name'      => 'required|unique:categories,name,'.$categoryId.',id,deleted_at,NULL',
            'type'      => 'required',
            'is_premium'=> 'required|in:0,1',
            'image'     => 'sometimes'
        ];
    }
}
