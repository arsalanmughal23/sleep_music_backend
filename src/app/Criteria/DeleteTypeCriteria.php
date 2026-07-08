<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class ReportTypeCriteria.
 *
 * @package namespace App\Criteria;
 */
class DeleteTypeCriteria extends BaseCriteria implements CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param string $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    protected $status = null;

    public function apply($model, RepositoryInterface $repository)
    {
        if ($this->isset('status')) {
            $model = $model->where('status', 1);
        }
        return $model;
    }
}
