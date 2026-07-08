<?php

namespace App\Criteria;

use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class ViewsCriteria.
 *
 * @package namespace App\Criteria;
 */
class ViewsCriteria extends BaseCriteria implements CriteriaInterface
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
        $model->select('category_id', DB::raw('count(category_id) as m'))->orderBy('m', 'DESC');
        return $model->groupBy('category_id');
//            ->Count('media_id')
//            ->orderBy('media_id', 'desc');
//            ->take(5)
//            ->get();

    }
}
