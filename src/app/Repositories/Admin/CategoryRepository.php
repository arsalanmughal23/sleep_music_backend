<?php

namespace App\Repositories\Admin;

use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CategoryRepository
 * @package App\Repositories\Admin
 * @version December 28, 2018, 6:53 pm UTC
 *
 * @method Category findWithoutFail($id, $columns = ['*'])
 * @method Category find($id, $columns = ['*'])
 * @method Category first($columns = ['*'])
 */
class CategoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'parent_id',
        'name',
        'type'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Category::class;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function saveRecord($request)
    {
        $input              = $request->only(['parent_id', 'name', 'type', 'image']);
        $input['is_premium']= $request->is_premium ? 1 : 0;
        $input['parent_id'] = ($input['parent_id'] == 0) ? null : $input['parent_id'];
        
        // if ($request->hasFile('ximage')) {
        //     $file = $request->file('ximage');

        //     $input['image'] = Storage::disk('s3')->put('public/category_images', $file);
        //     Storage::disk('s3')->setVisibility($input['image'], 'public');
        // }

        if($request->image){
            $updateData['image'] = $request->image;
        }

        $category = $this->create($input);
        return $category;
    }

    /**
     * @param $request
     * @param $category
     * @return mixed
     */
    public function updateRecord($request, $category)
    {
        $input              = $request->only(['parent_id', 'name', 'type', 'image']);
        $input['is_premium']= $request->is_premium ? 1 : 0;
        $input['parent_id'] = ($input['parent_id'] == 0) ? null : $input['parent_id'];

        if($request->image){
            $input['image'] = $request->image;
        }

        $category = $this->update($input, $category->id);
        return $category;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteRecord($id)
    {
        $category = $this->delete($id);
        return $category;
    }

    public function swapRows($request, $id)
    {
        $input    = $request;
        $category = $this->update($input, $id);
        return $category;
    }
}
