<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class TransactionCriteria.
 *
 * @package namespace App\Criteria;
 */
class TransactionCriteria extends BaseCriteria implements CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param string $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    protected $user_id = null;
    protected $status = null;

    public function apply($model, RepositoryInterface $repository)
    {
        if ($this->isset('user_id')) {
            $model = $model->where('user_id', $this->user_id);
        }
        if ($this->isset('status')) {
            $model = $model->where('status', $this->status);
        }
        $model = $model->orderBy('id', 'desc');
        return $model;
    }
}
