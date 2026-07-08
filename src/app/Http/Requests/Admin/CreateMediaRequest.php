<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Media;

class CreateMediaRequest extends FormRequest
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
        // return Media::$rules;
        return [
            'is_premium'  => 'required|in:1,0',
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|unique:media,name,NULL,id,deleted_at,NULL',
            'image'       => 'sometimes',
            'file'        => 'sometimes',

            'input_image' => 'required|max:'.config("constants.max_image_size_in_kb"),
            'input_file'  => 'required|max:'.config("constants.max_audio_size_in_kb")
        ];
    }
}
