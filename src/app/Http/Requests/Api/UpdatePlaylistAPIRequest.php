<?php

namespace App\Http\Requests\Api;

use App\Models\Playlist;

class UpdatePlaylistAPIRequest extends BaseAPIRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
//        $playlist = $this->route('playlist');
//        return ($playlist->user_id == \Auth::id());
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return Playlist::$api_update_rules;
    }
}
