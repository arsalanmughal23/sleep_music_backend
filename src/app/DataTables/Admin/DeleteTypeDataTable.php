<?php

namespace App\DataTables\Admin;

use App\Models\DeleteType;
use App\Models\ReportType;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

/**
 * Class DeleteTypeDataTable
 * @package App\DataTables\Admin
 */
class DeleteTypeDataTable extends DataTable
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

        $dataTable->editColumn('status', function (DeleteType $model) {
            if ($model->status == DeleteType::ACTIVE) {
                return 'Active';
            } else {
                return 'Inactive';
            }

        });

        return $dataTable->addColumn('action', 'admin.delete_types.datatables_actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\DeleteType $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(DeleteType $model)
    {
        return $model->newQuery()->orderBy('updated_at', 'desc');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        $buttons = [];
        if (\Entrust::can('delete-types.create') || \Entrust::hasRole('super-admin')) {
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
            'name',
            'status',
            'created_at'
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'delete_typesdatatable_' . time();
    }
}