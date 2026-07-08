<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Category;

class ImportMediaStepOneRequest extends FormRequest
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
            'file'              => 'required',
            'type'              => 'required',
            'has_headers'       => 'required',
            'create_artists'    => 'required',
            'create_categories' => 'required',
            'create_playlists'  => 'required',
        ];
    }
}
