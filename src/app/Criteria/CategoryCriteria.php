<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class CategoryCriteria.
 *
 * @package namespace App\Criteria;
 */
class CategoryCriteria extends BaseCriteria implements CriteriaInterface
{    
    protected $is_mixer             = null;
    protected $type                 = null;
    protected $with_media           = null;
    protected $query                = null;
    protected $exclude_empty        = null;

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
        if($this->isset('is_mixer')) {
            if($this->is_mixer !== null){
                $model = $model->where('is_mixer', $this->is_mixer);
            }
        }

        // Check if Type Property is set
        if ($this->isset('type')) {
            // If Type Property is set only include categories with that type
            $model = $model->where('type', $this->type);
        }

        if ($this->isset('with_media')) {
            $model = $model->with(['media_all' => function ($builder) {
                $builder->where(function($q){
                    $q->where(['is_mixer' => 1, 'user_id' => \Auth::id()]);
                })
                ->orWhere(function($q){
                    $q->where(['is_unlockable' => 1, 'user_id' => \Auth::id()]);
                })
                ->orWhere(function($q){
                    $q->where(['is_mixer' => 0, 'is_unlockable' => 0]);
                });
            }]);
        }

        // Check if Query Property is set
        if ($this->isset('query')) {
            // If Query Property is set, add a like condition to include only those categories who have this string in their name
            $model = $model->where('name', 'like', $this->getLikeString($this->query));
        }

        // Check if Exclude Empty Property is set
        if ($this->isset('exclude_empty')) {
            // If Exclude Empty is set, add a condition to include only those categories that have songs.
            $model = $model->whereHas('media_all');
        }

        return $model;

        
        // if ($this->isset('category_id')) {
        //     // If Type Property is set only include categories with that type
        //     $model = $model->where('category_id', $this->category_id);
        // }

        // if ($this->isset('with_playlists')) {
        //     $is_featured          = $this->playlist_is_featured;
        //     $playlist_child_only  = $this->playlist_child_only;
        //     $playlist_parent_only = $this->playlist_parent_only;
        //     $playlist_parent_id   = $this->playlist_parent_id;
        //     $playlist_has_child   = $this->playlist_has_child;

        //     $model = $model->with(['playlists_all' => function ($query) use ($is_featured, $playlist_child_only, $playlist_parent_only, $playlist_parent_id, $playlist_has_child) {
        //         $query->orderBy('created_at', 'DESC')
        //             ->when(($is_featured !== null), function ($q) use ($is_featured) {
        //                 return $q->where('is_featured', $is_featured);
        //             })->when(($playlist_child_only !== null), function ($q) {
        //                 return $q->whereNotNull('parent_id');
        //             })->when(($playlist_parent_only !== null), function ($q) {
        //                 return $q->where('parent_id', null);
        //             })->when(($playlist_parent_id !== null), function ($q) use ($playlist_parent_id) {
        //                 return $q->where('parent_id', $playlist_parent_id);
        //             })->when(($playlist_has_child !== null), function ($q) use ($playlist_has_child) {
        //                 return $q->where('has_child', $playlist_has_child);
        //             });
        //     }]);
        // }


        // Check if Sort By Songs Property is set
        // if ($this->isset('sort_by_songs')) {
        //     // If Sort By Songs is set, sort categories by song count.
        //     $model = $model->withCount('media_all')->orderBy('media_all_count', 'desc');
        // }

        // Check if Sort By Songs Property is set
        // if ($this->isset('trending')) {
        //     // If Sort By Songs is set, sort categories by song count.
        //     $model = $model->orderBy('views', 'DESC');
        // }

        // Check if Sort By Songs Property is set
        // if ($this->isset('popular')) {
        //     // If Sort By Songs is set, sort categories by song count.
        //     $model = $model->orderBy('views', 'DESC');
        // }

    }
}
