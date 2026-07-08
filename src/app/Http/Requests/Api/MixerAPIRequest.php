<?php

namespace App\Http\Requests\Api;

class MixerAPIRequest extends BaseAPIRequest
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
            'name' => 'required',
            'image' => 'required',
            'medias' => 'required|array|min:1|max:10',
            // 'medias.*' => 'required|object',
            'medias.*.id' => 'required|integer|exists:media,id',
            'medias.*.name' => 'required|string',
            'medias.*.volume' => 'required|numeric|min:0',//|between:0,1
            'medias.*.image' => 'required|url',
            'medias.*.file_url' => 'nullable|url',
        ];
    }
}
