<?php

namespace App\Criteria;

use http\Exception\BadMethodCallException;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class UserCriteria.
 *
 * @package namespace App\Criteria;
 */
class UserCriteria extends BaseCriteria implements CriteriaInterface
{
    protected $role          = null;
    protected $query         = null;
    protected $exclude_empty = null;
    protected $sort_by_songs = 1;
    protected $cacheFor      = null;
    protected $user_id       = null;


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
        // Check if Role Property is set
        if ($this->isset('role')) {
            // If Role Property is set, add a condition to include only those users who have this role
            $role  = $this->role;
            $model = $model->whereHas('roles', function ($q) use ($role) {
                return $q->where('id', $role);
            });
        }
        // Check if Query Property is set
        if ($this->isset('query')) {
            // If Query Property is set, add a like condition to include only those users who have this string in their name
            $query = $this->getLikeString($this->query);
//            $model = $model->where('name', 'like', $this->getLikeString($this->query));
            $model = $model->where('id', '!=', \Auth::id());
            $model = $model->whereHas('details', function ($q) use ($query) {
                return $q->where('first_name', 'like', $query);

            });
        }
        if ($this->isset('user_id')) {
            $model = $model->where('id', $this->user_id);
        }

        // Check if Exclude Empty Property is set
        if ($this->isset('exclude_empty')) {
            // If Exclude Empty is set, add a condition to include only those artists that have songs.
            $model = $model->whereHas('media');
        }

        // Check if Sort By Songs Property is set
        if ($this->isset('sort_by_songs')) {
            // If Sort By Songs is set, sort artists by song count.
            $model = $model->withCount('media')->orderBy('media_count', 'desc');
        }

        if ($this->isset('cacheFor')) {
            try {
                $model = $model->cacheFor($this->cacheFor);
            } catch (\BadMethodCallException $e) {

            }
        }
        return $model;
    }
}
