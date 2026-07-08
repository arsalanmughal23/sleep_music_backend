<?php

namespace App\Repositories\Admin;

use App\Models\Report;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ReportRepository
 * @package App\Repositories\Admin
 * @version September 14, 2021, 8:35 pm UTC
 *
 * @method Report findWithoutFail($id, $columns = ['*'])
 * @method Report find($id, $columns = ['*'])
 * @method Report first($columns = ['*'])
 */
class ReportRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'media_id',
        'report_type'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Report::class;
    }

    /**
     * @param $request
     * @return mixed
     */
    public function saveRecord($request)
    {
        $input  = $request->only('instance_id', 'description');
        $input['user_id'] = \Auth::id();
        $input['instance_type'] = 20;
        
        $report = $this->create($input);

        if ($request->input('report_type_ids')) {
            $reportTypeIds = is_array($request->report_type_ids) ? $request->report_type_ids : [];
            $report->types()->sync($reportTypeIds);
        }

        return $report;
    }

    /**
     * @param $request
     * @param $report
     * @return mixed
     */
    public function updateRecord($request, $reportId)
    {
        $input  = $request->only('instance_id', 'description');
        $report = $this->update($input, $reportId);

        if (isset($request->report_type_ids) && is_array($request->report_type_ids)) {
            if (count($request->report_type_ids) > 0) {
                $reportTypeIds = $request->report_type_ids;
                $report->types()->sync($reportTypeIds);
            } else {
                $report->types()->detach($report->types->pluck('id'));
            }
            
        }
        return $report->refresh();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteRecord($id)
    {
        $report = $this->delete($id);
        return $report;
    }
}
