<?php

namespace App\Repositories\Admin;

use App\Models\TrendingArtist;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TrendingArtistRepository
 * @package App\Repositories\Admin
 * @version October 28, 2021, 11:15 am UTC
 *
 * @method TrendingArtist findWithoutFail($id, $columns = ['*'])
 * @method TrendingArtist find($id, $columns = ['*'])
 * @method TrendingArtist first($columns = ['*'])
 */
class TrendingArtistRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'user_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TrendingArtist::class;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function saveRecord($request)
    {
        $input            = $request->all();
        $input['user_id'] = \Auth::id();
        $trendingArtist   = $this->updateOrCreate($input);
        return $trendingArtist;
    }

    /**
     * @param $request
     * @param $trendingArtist
     * @return mixed
     */
    public function updateRecord($request, $trendingArtist)
    {
        $input            = $request->all();
        $input['user_id'] = \Auth::id();

        $trendingArtist = $this->updateOrCreate($input, $trendingArtist->id);
        return $trendingArtist;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteRecord($id)
    {
        $trendingArtist = $this->delete($id);
        return $trendingArtist;
    }
}
