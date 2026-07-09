<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class NotifcationUserCriteria.
 *
 * @package namespace App\Criteria;
 */
class NotifcationUserCriteria extends BaseCriteria implements CriteriaInterface
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

    public function apply($model, RepositoryInterface $repository)
    {
        if ($this->isset('is_mine')) {
            $user_id = \Auth::id();
            $model   = $model->where('user_id', $user_id)->orderBy('id', 'desc');
        }
        return $model;
    }
}
