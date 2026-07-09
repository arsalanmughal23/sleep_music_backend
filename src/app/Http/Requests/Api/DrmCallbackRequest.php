<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

/**
 * @property mixed asset
 * @property mixed variant
 */
class DrmCallbackRequest extends BaseAPIRequest
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
            'asset'                      => 'required',
//            'variant'                    => 'required',
            'user'                       => 'required',
            'session'                    => 'required',
            'client'                     => 'required',
            'drmScheme'                  => 'required|in:FAIRPLAY,WIDEVINE_CLASSIC,WIDEVINE_MODULAR,PLAYREADY,OMADRM',
            'clientInfo'                 => 'required',
            'requestMetadata'            => 'required',
            'requestMetadata.remoteAddr' => 'required',
            'requestMetadata.userAgent'  => 'required',
        ];
    }

    /**
     * @param Validator $validator
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $except = [];
        $except = new ValidationException($validator);
        $except->errorBag($this->errorBag)->redirectTo($this->getRedirectUrl());
        $except->status = 422;
        foreach ($except->errors() as $key => $error) {
            $errors[] = [
                'label'   => $key,
                'message' => $error[0]
            ];
        }
        $except->response = \Response::json([
            'message' => $errors[0]['message']
        ], $except->status);
//        $except->response = Response::json($this->makeError($errors[0]['message'], [], $errors), $except->status);
        throw $except;
    }
}
