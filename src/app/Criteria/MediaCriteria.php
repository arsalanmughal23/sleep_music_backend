<?php

namespace App\Criteria;

use App\Repositories\Admin\FollowRepository;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class MediaCriteria.
 *
 * @package namespace App\Criteria;
 */
class MediaCriteria extends BaseCriteria implements CriteriaInterface
{
    protected $category_id = null;
    protected $is_mine     = null;
    protected $is_mixer = null;
    protected $is_unlockable = null;
    protected $user_id     = null;
    protected $query       = null;

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
        if ($this->isset('category_id')) {
            $model = $model->where('category_id', $this->category_id);
        }
        
        if ($this->isset('is_mine') && $this->is_mine) {
            $model = $model->where('user_id', \Auth::id());
        }

        if ($this->isset('is_mixer')) {
            $model = $model->where('is_mixer', $this->is_mixer);
        }

        if ($this->isset('is_unlockable')) {
            $model = $model->where('is_unlockable', $this->is_unlockable)
                ->whereHas('userMedia', function($userUnlockedMedia){
                    $userUnlockedMedia->where('user_id', \Auth::id());
                });
        }

        if ($this->isset('user_id')) {
            $model = $model->where('user_id', $this->user_id);
        }

        if ($this->isset('query')) {
            $model = $model->where('name', 'like', $this->getLikeString($this->query));
        }

        $model = $model->orderBy('id', 'desc');
        return $model;
    }
}
