<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class CardCriteria.
 *
 * @package namespace App\Criteria;
 */
class CardCriteria extends BaseCriteria implements CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param string $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    protected $user_id;
    protected $is_default;

    public function apply($model, RepositoryInterface $repository)
    {
//        dd($this->user_id);
        if ($this->isset('user_id')) {

            $model = $model->where('user_id', $this->user_id);
        }
//        if ($this->isset('is_default')) {
//
//            $model = $model->where('is_default', 1);
//        }

        return $model;
    }
}
