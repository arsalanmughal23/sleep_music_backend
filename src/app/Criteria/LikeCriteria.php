<?php

namespace App\Criteria;

use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;


/**
 * Class LikeCriteria.
 *
 * @package namespace App\Criteria;
 */
class LikeCriteria extends BaseCriteria implements CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param string $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */

    protected $is_mine;
    protected $media_id;
    protected $is_popular;

    public function apply($model, RepositoryInterface $repository)
    {
        if ($this->isset('is_mine')) {
            $model = $model->where('followed_by_user_id', \Auth::id());
        }
        if ($this->isset('media_id')) {
            return $model = $model->where('media_id', $this->media_id);

        }
        if ($this->isset('is_popular')) {
//            $model->groupBy('category_id');
            return $model->select('category_id', DB::raw('count(category_id) as m'))->groupBy('category_id')->orderBy('m', 'DESC')->take(5);

        }


    }
}
