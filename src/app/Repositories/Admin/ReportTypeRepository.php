<?php

namespace App\Repositories\Admin;

use App\Models\Report;
use App\Models\ReportType;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ReportTypeRepository
 * @package App\Repositories\Admin
 * @version September 21, 2021, 7:29 pm UTC
 *
 * @method ReportType findWithoutFail($id, $columns = ['*'])
 * @method ReportType find($id, $columns = ['*'])
 * @method ReportType first($columns = ['*'])
*/
class ReportTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'type'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ReportType::class;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function saveRecord($request)
    {
        $input = $request->all();
        $input['type'] = Report::INSTANCE_TYPE_CONTENT;
        $reportType = $this->create($input);
        return $reportType;
    }

    /**
     * @param $request
     * @param $reportType
     * @return mixed
     */
    public function updateRecord($request, $reportType)
    {
        $input = $request->all();
        $input['type'] = Report::INSTANCE_TYPE_CONTENT;
        $reportType = $this->update($input, $reportType->id);
        return $reportType;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteRecord($id)
    {
        $reportType = $this->delete($id);
        return $reportType;
    }
}
