<?php

namespace App\Criteria;

use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use Illuminate\Support\Facades\Auth;

/**
 * Class FollowCriteria.
 *
 * @package namespace App\Criteria;
 */
class FollowCriteria extends BaseCriteria implements CriteriaInterface
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
    protected $followers;
    protected $following;
    protected $is_popular_artist;
    protected $user_id;
    protected $user_id_folow;
    protected $followed_by_user_id;

    public function apply($model, RepositoryInterface $repository)
    {

        if ($this->is_mine == 1) {

            if ($this->isset('is_mine') && $this->isset('following')) {
                $model = $model->where('followed_by_user_id', \Auth::id());
            }
            if ($this->isset('is_mine') && $this->isset('followers')) {
                $model = $model->where('followed_user_id', \Auth::id())->with('follower')->without('user');
            }

        }
        if ($this->is_mine == 0) {
//            dd($this->user_id_folow, $this->followers);
            if ($this->isset('user_id_folow') && $this->isset('following')) {
                $model = $model->where('followed_by_user_id', $this->user_id_folow);
            }
            if ($this->isset('is_mine') && $this->isset('followers')) {
                $model = $model->where('followed_user_id', $this->user_id_folow)->with('follower')->without('user');
            }
        }

        if ($this->isset('user_id') && $this->isset('following')) {
            $model = $model->where('followed_by_user_id', $this->user_id);
        }

        if ($this->isset('user_id') && $this->isset('followers')) {
            $model = $model->where('followed_user_id', $this->user_id)->with('follower')->without('user');
        }
        if ($this->isset('is_popular_artist')) {
            return $model->select('followed_user_id', DB::raw('count(followed_user_id) as m'))->groupBy('followed_user_id')->orderBy('m', 'DESC');
        }
        return $model;
    }
}
