<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class CommentCriteria.
 *
 * @package namespace App\Criteria;
 */
class CommentCriteria extends BaseCriteria implements CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param string $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */

    protected $media_id;
    protected $parent_id;

    public function apply($model, RepositoryInterface $repository)
    {

        if ($this->isset('media_id')) {
            if ($this->isset('parent_id')) {
                return $model->where('parent_id', $this->parent_id)->where('media_id', $this->media_id);
            } else {
                return $model->where('media_id', $this->media_id)->where('parent_id', '=', NULL);
            }

        }

    }

}
