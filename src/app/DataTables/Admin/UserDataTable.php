<?php

namespace App\DataTables\Admin;

use App\Helper\Util;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

/**
 * Class UserDataTable
 * @package App\DataTables\Admin
 */
class UserDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        /**
         * for master detail search uncomment next lines
         */

        $dataTable = new EloquentDataTable($query);

        $dataTable->editColumn('details.first_name', function (User $model) {
            return $model->details->first_name ?? "";
        });

        $dataTable->editColumn('details.last_name', function (User $model) {
            return $model->details->last_name ?? "";
        });

        $dataTable->addColumn('roles', function ($user) {
            return $user->rolesCsv;
        });

        $dataTable->editColumn('status', function (User $model) {
            if ($model->status) {
                return '<span class="label label-success">Active</span>';
            } else {
                return '<span class="label label-danger">In-Active</span>';
            }
        });

        $dataTable->addColumn('is_subscriber', function (User $model) {
            if ($model->is_subscriber) {
                return '<span class="label label-success">Yes</span>';
            } else {
                return '<span class="label label-danger">No</span>';
            }
        });

        $dataTable->editColumn('image', function (User $model) {
            if (isset($model->details) && strpos($model->details->image, 'http') !== false) {
                return isset($model->details->image) ? '<img style="width:50px;" src="' . $model->details->image . '"  />' : "N/A";
            } else {
                return isset($model->details->image_url) ? '<img style="width:50px;" src="' . $model->details->image_url . '"  />' : "N/A";
            }

        });

        $dataTable->rawColumns(['action', 'is_subscriber', 'status', 'image']);

        return $dataTable->addColumn('action', 'admin.users.datatables_actions');
    }


    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        $roleIncluded = [Role::ROLE_AUTHENTICATED];
        if(Auth::user()->hasRole('super-admin')){
            array_push($roleIncluded, Role::ROLE_ADMIN);
        }

        $model = $model->newQuery();
        
        $model->whereHas('roles', function($role) use($roleIncluded) {
            $role->whereIn('id', $roleIncluded);
        })->select('users.*');

        if($this->status != null){
            $model->where('status', $this->status);
        }
        if($this->subscription != null){
            if($this->subscription == 1){
                $model->whereHas('userAllSubscriptions');
            }else{
                $model->whereDoesntHave('userAllSubscriptions');
            }
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
        if (\Entrust::can('users.create') || \Entrust::hasRole('super-admin')) {
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
            'id' => ['title' => 'ID', 'searchable' => false],
            'details.first_name' => ['searchable' => false, 'title' => 'First Name', 'orderable' => true],
            'details.last_name' => ['searchable' => false, 'title' => 'Last Name', 'orderable' => true],
            'name' => ['searchable' => true, 'title' => 'Full Name', 'orderable' => false, 'class' => 'hidden'],
            'email',
            'is_subscriber' => ['searchable' => false, 'title' => 'Is Subscriber', 'orderable' => false],
            'status' => ['searchable' => false, 'title' => 'Status', 'orderable' => true],
            'image'              => ['searchable' => false, 'orderable' => false],
            // 'roles'              => ['searchable' => false, 'title' => 'Roles', 'orderable' => false],
            // 'status'             => ['searchable' => false, 'orderable' => false],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'usersdatatable_' . time();
    }
}