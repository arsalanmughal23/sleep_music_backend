<?php

namespace App\DataTables\Admin;

use App\Helper\Util;
use App\Models\Notification;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

/**
 * Class NotificationDataTable
 * @package App\DataTables\Admin
 */
class NotificationDataTable extends DataTable
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

//        $dataTable->editColumn('url', function (Notification $model) {
//            return $model->url ?? "";
//
//        });
        $dataTable->editColumn('action_type', function (Notification $model) {
            return $model->action_type ?? "";

        });
        $dataTable->editColumn('media.name', function (Notification $model) {
            if (isset($model->media->name)) {
                return $model->media->name;
            } else if (isset($model->sender->name)) {
                return $model->sender->name;
            } else {
                return 'Donation';
            }


        });
        $dataTable->editColumn('message', function (Notification $model) {
//            return $model->message ?? "";
            if (isset($model->sender->name)) {
                return isset($model->message) ? str_replace('[name]', $model->sender->name, $model->message) : "0";
            } else {
                return 'N/A';
            }

        });
        $dataTable->editColumn('created_at', function (Notification $model) {
            return $model->created_at ?? "";

        });


        return $dataTable->addColumn('action', 'admin.notifications.datatables_actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Notification $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Notification $model)
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
        if (\Entrust::can('notifications.create') || \Entrust::hasRole('super-admin')) {
            $buttons = ['create'];
        }
        $buttons = array_merge($buttons, [
            'excel',
            'csv',
            'print',
            'reset',
            'reload',
        ]);
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['width' => '80px', 'printable' => false])
            ->parameters(array_merge(Util::getDataTableParams(), [
                'dom'     => 'Blfrtip',
                'order'   => [[0, 'desc']],
                'buttons' => $buttons,
            ]));
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'id',
//            'url',
            'action_type',
            'media.name' => ['searchable' => false, 'title' => 'User', 'orderable' => false],
            'message',
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
        return 'notificationsdatatable_' . time();
    }
}