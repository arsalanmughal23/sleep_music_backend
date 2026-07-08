<?php

namespace App\DataTables\Admin;

use App\Models\Report;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

/**
 * Class ReportDataTable
 * @package App\DataTables\Admin
 */
class ReportDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $query     = $query->with(['user', 'media', 'types']);
        $dataTable = new EloquentDataTable($query);
        
        $dataTable->editColumn('media.name', function (Report $report) {
            return '<a href="' . route('admin.medias.show', $report->instance_id) . '" style="font-weight:bold">' . ($report->media->name ?? '-') . '</a>';
        });

        $dataTable->editColumn('status', function (Report $model) {
            if ($model->status) {
                return '<span class="label label-success">Resolved</span>';
            } else {
                return '<span class="label label-danger">Under Investigation</span>';
            }
        });

        $dataTable->editColumn('types.count', function (Report $model) {
            return $model->types->count();
        });

        $dataTable->editColumn('user.name', function (Report $model) {
            return isset($model->user) && ($model->user->details) ? 
                '<a href="' . route('admin.users.show', $model->user->id) . '" style="font-weight:bold">' . $model->user->details->full_name . '</a>' : 'N/A';
        });

        $dataTable->rawColumns(['id', 'status', 'action', 'media.name', 'user.name']);
        return $dataTable->addColumn('action', 'admin.reports.datatables_actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Report $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Report $model)
    {
        $model = $model->newQuery();
        if ($this->report_type_id != null) {
            $model->whereHas('types', function($query){
                $query->where('report_type_id', $this->report_type_id);
            });
        }
        if ($this->status != null) {
            $model->where('status', $this->status);            
        }
        return $model;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        $buttons = [];
        if (\Entrust::can('reports.create') || \Entrust::hasRole('super-admin')) {
            $buttons = ['create'];
        }
        $buttons = array_merge($buttons, [
            'excel',
            'csv',
            'print',
            // 'reset',
            'reload',
        ]);
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['width' => '80px', 'printable' => false])
            ->parameters([
                'dom'     => 'Bfrtip',
                'order'   => [[0, 'desc']],
                'buttons' => $buttons,
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'id'            => ['title' => 'ID', 'searchable' => false],
            'user.name'     => ['title' => 'User Name', 'searchable' => true],
            'media.name'    => ['title' => 'Report Against', 'searchable' => true],
            'status'        => ['title' => 'Status', 'searchable' => true],
            // 'types.count'    => ['title' => 'Report Types Count', 'searchable' => true],
            // 'description',
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'reportsdatatable_' . time();
    }
}