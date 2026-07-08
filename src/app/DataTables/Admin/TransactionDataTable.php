<?php

namespace App\DataTables\Admin;

use App\Models\Transaction;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

/**
 * Class TransactionDataTable
 * @package App\DataTables\Admin
 */
class TransactionDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $query     = $query->with(['user']);
        $dataTable = new EloquentDataTable($query);
        $dataTable->editColumn('user.details.first_name', function (Transaction $model) {
//            return $model->user && $model->user->details ? $model->user->details->full_name : 'N/A';
//            return isset($model->user) && ($model->user->name) ?
//            return '<a href="' . url('/admin/users/' . $model->user_id) . '" style="font-weight:bold">' . $model->user->details->full_name . '</a> ';

        return isset($model->user) && ($model->user->name) ? '<a href="' . url('/admin/users/' . $model->user_id) . '" style="font-weight:bold">' . $model->user->name . '</a> ' : 'N/A';
        });
        $dataTable->editColumn('currency', function (Transaction $model) {
            return $model->currency ? strtoupper($model->currency) : 'N/A';
        });
        $dataTable->editColumn('status', function (Transaction $model) {
            return $model->status_badge;
        });
        $dataTable->editColumn('type', function (Transaction $model) {
            if ($model->type == Transaction::PAY_TYPE_PAYPAL) {
                return '<span class="label label-success">PAYPAL</span>';
            } else {
                return '<span class="label label-success">STRIPE</span>';
            }

        });

        $dataTable->rawColumns(['status', 'action', 'user.details.first_name', 'type']);
        return $dataTable->addColumn('action', 'admin.transactions.datatables_actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Transaction $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Transaction $model)
    {
        return $model->newQuery();
            // ->orderBy('updated_at', SORT_DESC);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        $buttons = [];
        if (\Entrust::can('transactions.create') || \Entrust::hasRole('super-admin')) {
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
            'user.details.first_name' => ['title' => 'User', 'orderable' => false, 'searchable' => false],
            'currency'  => ['title' => 'Currency'],
            'amount'    => ['title' => 'Total Amount'],
            'type'      => ['title' => 'Account Type'],
            'status',
            'description',
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
        return 'transactionsdatatable_' . time();
    }
}