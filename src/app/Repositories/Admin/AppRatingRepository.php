<?php

namespace App\Repositories\Admin;

use App\Models\AppRating;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AppRatingRepository
 * @package App\Repositories\Admin
 * @version August 17, 2023, 12:40 pm UTC
 *
 * @method AppRating findWithoutFail($id, $columns = ['*'])
 * @method AppRating find($id, $columns = ['*'])
 * @method AppRating first($columns = ['*'])
*/
class AppRatingRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'user_id',
        'rating',
        'created_at'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AppRating::class;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function saveRecord($request)
    {
        $input = $request->all();
        $appRating = $this->create($input);
        return $appRating;
    }

    /**
     * @param $request
     * @param $appRating
     * @return mixed
     */
    public function updateRecord($request, $appRating)
    {
        $input = $request->all();
        $appRating = $this->update($input, $appRating->id);
        return $appRating;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteRecord($id)
    {
        $appRating = $this->delete($id);
        return $appRating;
    }
}
