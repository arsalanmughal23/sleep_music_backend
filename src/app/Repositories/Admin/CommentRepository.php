<?php

namespace App\Repositories\Admin;

use App\Models\Comment;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CommentRepository
 * @package App\Repositories\Admin
 * @version September 14, 2021, 8:29 pm UTC
 *
 * @method Comment findWithoutFail($id, $columns = ['*'])
 * @method Comment find($id, $columns = ['*'])
 * @method Comment first($columns = ['*'])
 */
class CommentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'comment',
        'music_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Comment::class;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function saveRecord($request)
    {
        $input            = $request->all();
        $input['user_id'] = \Auth::id();
        $comment          = $this->create($input);
        return $comment;
    }

    /**
     * @param $request
     * @param $comment
     * @return mixed
     */
    public function updateRecord($request, $id)
    {
        $input   = $request->all();
        $comment = $this->update($input, $id);
        return $comment;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteRecord($id)
    {
        $comment = $this->delete($id);
        return $comment;
    }
}
