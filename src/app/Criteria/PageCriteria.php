<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class PageCriteria.
 *
 * @package namespace App\Criteria;
 */
class PageCriteria extends BaseCriteria implements CriteriaInterface
{
    protected $slug = null;

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
        // Check if Slug Property is set
        if ($this->isset('slug')) {
            // If Slug Property is set only include pages with that slug
            $model = $model->where('slug', $this->slug);
        }

        return $model;
    }
}
