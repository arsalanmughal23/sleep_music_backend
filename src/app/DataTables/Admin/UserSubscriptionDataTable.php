<?php

namespace App\DataTables\Admin;

use App\Models\UserSubscription;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

/**
 * Class UserSubscriptionDataTable
 * @package App\DataTables\Admin
 */
class UserSubscriptionDataTable extends DataTable
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
        
        $dataTable->editColumn('user.name', function (UserSubscription $model) {
            return isset($model->user) && ($model->user->details) ? 
                '<a href="' . route('admin.users.show', $model->user->id) . '" style="font-weight:bold">' . $model->user->details->full_name . '</a>' : 'N/A';
        });
        
        $dataTable->editColumn('currency', function (UserSubscription $model) {
            return $model->currency ?? '-';
        });

        $dataTable->editColumn('amount', function (UserSubscription $model) {
            return $model->amount ?? '-';
        });
        
        $dataTable->editColumn('expiry_date', function (UserSubscription $model) {
            return $model->expiry_date ?? '-';
        });
        
        $dataTable->addColumn('trial', function (UserSubscription $model) {
            return $model->offer_discount_type;
        });

        $dataTable->editColumn('status', function (UserSubscription $model) {
            return $model->status_badge;
        });

        $dataTable->rawColumns(['user.name', 'status', 'trial', 'action']);

        return $dataTable->addColumn('action', 'admin.user_subscriptions.datatables_actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\UserSubscription $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(UserSubscription $model)
    {
        $model = $model->newQuery();

        if($this->status != null){
            $model->where('status', $this->status);
        }

        if($this->subscription_name != null){
            $model->where('product_id', 'like', '%'.$this->subscription_name);
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
        if (\Entrust::can('user-subscriptions.create') || \Entrust::hasRole('super-admin')) {
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
            'user.name',
            'currency',
            'amount',
            'expiry_date',
            // 'platform',
            'product_id',
            'status',
            // 'trial',
            'created_at' => ['searchable' => false, 'title' => 'Created At', 'orderable' => true],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'user_subscriptionsdatatable_' . time();
    }
}