<?php

namespace App\Repositories\Admin;

use App\Models\Card;
use Illuminate\Http\Request;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CardRepository
 * @package App\Repositories\Admin
 * @version December 16, 2021, 12:29 pm UTC
 *
 * @method Card findWithoutFail($id, $columns = ['*'])
 * @method Card find($id, $columns = ['*'])
 * @method Card first($columns = ['*'])
 */
class CardRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'payment_method'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Card::class;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function saveRecord($request)
    {
        $input = $request instanceof Request ? $request->all() : $request;
        $card  = $this->create($input);
        return $card;
    }

    /**
     * @param $request
     * @param $card
     * @return mixed
     */
    public function updateRecord($request, $card)
    {
        $input = $request->all();
        $card  = $this->update($input, $card->id);
        return $card;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteRecord($id)
    {
        $card = $this->delete($id);
        return $card;
    }
}
