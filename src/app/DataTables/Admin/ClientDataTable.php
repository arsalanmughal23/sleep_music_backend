<?php

namespace App\DataTables\Admin;

use App\Helper\Util;
use App\Models\Client;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

/**
 * Class ClientDataTable
 * @package App\DataTables\Admin
 */
class ClientDataTable extends DataTable
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

        $dataTable->editColumn('status', function (Client $client) {
            return '<span class="label label-' . Util::getBoolCss($client->status) . '">' . Util::getBoolText($client->status) . '</span>';
        });
        $dataTable->editColumn('connection_status', function (Client $client) {
            return '<span class="label label-' . Util::getBoolCss($client->connection_status) . '">' . Util::getBoolText($client->connection_status, "Connected", "Disconnected") . '</span>';
        });

        $dataTable->rawColumns(['action', 'status', 'connection_status']);
        return $dataTable->addColumn('action', 'admin.clients.datatables_actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Client $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Client $model)
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
        if (\Entrust::can('clients.create') || \Entrust::hasRole('super-admin')) {
            $buttons = ['create'];
        }
        $buttons = array_merge($buttons, [
            'export',
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
            'name',
            'cidr' => ['title' => 'CIDR'],
            'connection_limit',
            'connection_status',
            'status'
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'clientsdatatable_' . time();
    }
}