<?php

namespace App\Criteria;

use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class TrendingArtistCriteria.
 *
 * @package namespace App\Criteria;
 */
class TrendingArtistCriteria extends BaseCriteria implements CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param string $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        return $model->select('artist_id', DB::raw('count(artist_id) as m'))->groupBy('artist_id')->orderBy('m', 'DESC');
    }
}
