<?php

namespace App\DataTables\Admin;

use App\Helper\Util;
use App\Models\ClientConnectionLog;
use Carbon\Carbon;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

/**
 * Class ClientConnectionLogDataTable
 * @package App\DataTables\Admin
 */
class ClientConnectionLogDataTable extends DataTable
{
    public $client_id = null;

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $query     = $query->with('client');
        $dataTable = new EloquentDataTable($query);
        $dataTable->editColumn('status', function (ClientConnectionLog $client) {
            return '<span class="label label-' . Util::getBoolCss($client->status) . '">' . Util::getBoolText($client->status, "Connected", "Disconnected") . '</span>';
        });
        // CarbonInterface::DIFF_ABSOLUTE = 1
        $dataTable->editColumn('human_readable', function (ClientConnectionLog $clientConnectionLog) {
            return $clientConnectionLog->seconds_until_next == 0 ? "Current Status" :
                Carbon::now()->addSeconds($clientConnectionLog->seconds_until_next)->diffForHumans(null, 1, false, 6);
        });
        $dataTable->rawColumns(['status']);
//        return $dataTable->addColumn('action', 'admin.client_connection_logs.datatables_actions');
        return $dataTable;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\ClientConnectionLog $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ClientConnectionLog $model)
    {
        if ($this->client_id != null) {
            return $model->newQuery()->where('client_id', $this->client_id);
        }
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
//        if (\Entrust::can('client-connection-logs.create') || \Entrust::hasRole('super-admin')) {
//            $buttons = ['create'];
//        }
        $buttons = array_merge($buttons, [
            'export',
            'print',
            'reset',
            'reload',
        ]);
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
//            ->addAction(['width' => '80px', 'printable' => false])
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
            'id',
//            'client.name',
            'status'         => [
                'orderable' => false
            ],
            'human_readable' => [
                'title'     => 'Total Time Spent',
                'orderable' => false
            ],
            'created_at'     => [
                'title'     => 'Started At',
                'orderable' => false,
            ]
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'client_connection_logsdatatable_' . time();
    }
}