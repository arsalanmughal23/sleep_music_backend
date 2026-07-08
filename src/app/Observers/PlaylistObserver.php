<?php

namespace App\Observers;

use App\Models\Playlist;

class PlaylistObserver
{
    public function created(Playlist $playlist)
    {
        if ($playlist->parent && (!$playlist->parent->has_child)) {
            // Has Parent, but parent does not have has_child=1;
            $this->updateParent($playlist->parent);
        }
    }

    public function updateParent(Playlist $playlist)
    {
        $playlist->has_child = $playlist->children()->count() > 0;
        $playlist->save();
    }

    public function updated(Playlist $playlist)
    {
        if ($playlist->isDirty('parent_id')) {
            $this->updateParent($playlist->parent);
        }
    }

    public function deleted(Playlist $playlist)
    {
        //  $this->updateParent($playlist->parent);
    }
}
