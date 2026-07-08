<?php

namespace App\DataTables\Admin;

use App\Models\ReportType;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

/**
 * Class ReportTypeDataTable
 * @package App\DataTables\Admin
 */
class ReportTypeDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);

        $dataTable->editColumn('type', function (ReportType $model) {
            if ($model->type == ReportType::$REPORT_TYPE_ACCOUNT) {
                return 'Account';
            } else {
                return 'Content';
            }

        });

        return $dataTable->addColumn('action', 'admin.report_types.datatables_actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\ReportType $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ReportType $model)
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        $buttons = [];
        if (\Entrust::can('report-types.create') || \Entrust::hasRole('super-admin')) {
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
            'id' => ['title' => 'ID', 'searchable' => false],
            'name' => ['searchable' => true],
            // 'type' => ['searchable' => false]
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'report_typesdatatable_' . time();
    }
}