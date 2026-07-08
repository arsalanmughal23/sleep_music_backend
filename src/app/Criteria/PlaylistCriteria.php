<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class PlaylistCriteria.
 *
 * @package namespace App\Criteria;
 */
class PlaylistCriteria extends BaseCriteria implements CriteriaInterface
{
    protected $type          = null;
    protected $parent_only   = null;
    protected $child_only    = null;
    protected $is_featured   = null;
    protected $is_protected  = null;
    protected $is_mine       = null;
    protected $with_media    = null;
    protected $query         = null;
    protected $updated_after = null;
    protected $exclude_empty = null;
    protected $sort_by_songs = null;
    protected $category_id   = null;
    protected $parent_id     = null;
    protected $has_child     = null;

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
        // Check if is_featured Property is set
        if ($this->isset('is_featured')) {
            // If is_featured Property is set to true,
            //  add a condition to include only those playlists who have is_featured=1
            //  else include only those playlists who have is_featured=0
            if ($this->is_featured) {
                $model = $model->where('is_featured', 1);
            } else {
                $model = $model->where('is_featured', 0);
            }
        }
        // Check if is_protected Property is set
        if ($this->isset('is_protected')) {
            // If is_protected Property is set to true,
            //  add a condition to include only those playlists who have is_protected=1
            //  else include only those playlists who have is_protected=0
            if ($this->is_protected) {
                $model = $model->where('is_protected', 1);
            } else {
                $model = $model->where('is_protected', 0);
            }
        }
        // Check if mine Property is set
        if ($this->isset('is_mine')) {
            // If mine Property is set,
            //  add a condition to include only those playlists which have user_id=\Auth::id()
            //  dont use cache if user requested their own playlists.
            $model = $model->where('user_id', \Auth::id())->dontCache();

        }
        // Check if with_media Property is set
        if ($this->isset('with_media')) {
            // If with_media Property is set,
            //  execute with method to include media too.
            $model = $model->with('media_all');
        }

        // Check if Type Property is set
        if ($this->isset('type')) {
            // If Type Property is set only include playlists with that type
            $model = $model->where('type', $this->type);
        }


        // Check if Query Property is set
        if ($this->isset('query')) {
            // If Query Property is set, add a like condition to include only those playlist who have this string in their name
            $model = $model->where('name', 'like', $this->getLikeString($this->query));
        }

        // Check if Updated After Property is set
        if ($this->isset('updated_after')) {
            // If Updated After is set, add a condition to include only those playlist which are updated after the given time.
            $model = $model->where('updated_at', '>', $this->updated_after);
        }

        // Check if Exclude Empty Property is set
        if ($this->isset('exclude_empty')) {
            // If Exclude Empty is set, add a condition to include only those playlist that have songs.
            $model = $model->whereHas('media_all');
        }
        // Check if Sort By Songs Property is set
        if ($this->isset('sort_by_songs')) {
            // If Sort By Songs is set, sort playlists by song count.
            $model = $model->withCount('media_all')->orderBy('media_all_count', 'desc');
        }

        // Check if Parent Only Property is set
        if ($this->isset('parent_only')) {
            // If Parent Only is set, add a condition to include only those playlists that does not have any parent.
            $model = $model->where('parent_id', NULL);
        }

        // Check if Child Only Property is set
        if ($this->isset('child_only')) {
            // If Parent Only is set, add a condition to include only those playlists that does not have any parent.
            $model = $model->whereNotNull('parent_id');
        }

        // Check if Category ID Property is set
        if ($this->isset('category_id')) {
            // If Category ID is set, add a condition to include only those playlists that belongs to this category.
            $model = $model->where('category_id', $this->category_id);
        }
        // Check if Parent ID Property is set
        if ($this->isset('parent_id')) {
            // If Parent ID is set, add a condition to include only those playlists that belongs to this category.
            $model = $model->where('parent_id', $this->parent_id);
        }
        // Check if Has Child Property is set
        if ($this->isset('has_child')) {
            // If Has Child is set to 1, only include those playlist that have children.
            // If Has Child is set to 0, only include those playlist that does not have children.
            $model = $model->where('has_child', $this->has_child);
        }

        if (\Auth::check()) {
            $model = $model->where('user_id', \Auth::id());
        }
        return $model;
    }
}
