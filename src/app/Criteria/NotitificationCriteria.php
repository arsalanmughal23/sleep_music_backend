<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;


/**
 * Class NotitificationCriteria.
 *
 * @package namespace App\Criteria;
 */
class NotitificationCriteria extends BaseCriteria implements CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param string $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */

    protected $is_mine = null;


    public function apply($model, RepositoryInterface $repository)
    {

        if ($this->isset('is_mine')) {
            $user_id = \Auth::id();
            $model   = $model->where('sender_id', $user_id);
        }

        return $model;
    }
}
