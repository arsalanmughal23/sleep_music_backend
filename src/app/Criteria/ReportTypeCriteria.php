<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class ReportTypeCriteria.
 *
 * @package namespace App\Criteria;
 */
class ReportTypeCriteria extends BaseCriteria implements CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param string $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    protected $type = null;

    public function apply($model, RepositoryInterface $repository)
    {
        if ($this->isset('type')) {
            $model = $model->where('type', $this->type);
        }
        return $model;
    }
}
