<?php

namespace App\DataTables\Admin;

use App\Helper\Util;
use App\Models\Category;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

/**
 * Class CategoryDataTable
 * @package App\DataTables\Admin
 */
class CategoryDataTable extends DataTable
{
    public $media_type = null;

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $query     = $query->with(['parent']);
        $dataTable = new EloquentDataTable($query);

        $dataTable->editColumn('parent_id', function (Category $model) {
            return $model->parent ? $model->parent->name : Util::getNone();
        });

//        $dataTable->editColumn('image', function (Category $model) {
//            return '<img src="' . $model->image_url . '" style="max-width: 150px" />';
//        });

        $dataTable->editColumn('type', function (Category $model) {
            return $model->type_text ?? "";
        });

        $dataTable->editColumn('is_premium', function (Category $model) {
            if ($model->is_premium) {
                return '<span class="label label-success">Yes</span>';
            } else {
                return '<span class="label label-danger">No</span>';
            }
        });

        $dataTable->editColumn('name', function (Category $model) {
            return $model->name ?? "";
        });

        $dataTable->editColumn('media_all_count', function (Category $model) {
            return $model->media_all_count;

        });

        $dataTable->editColumn('position', function (Category $category) {
            return '<span class="position" data-id="' . $category->id . '">' . $category->position . '</span>';
        });


//        $dataTable->editColumn('status', function (Category $model) {
////            dd($model);
//            if (isset($model->deleted_at)) {
//                return '<span class="label label-danger">Inactive</span>';
//            } else   return "<span class='label label-success'>Active</span>";
//        });
        $dataTable->rawColumns(['parent_id', 'is_premium', 'action', 'position']);
        return $dataTable->addColumn('action', 'admin.categories.datatables_actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Category $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Category $model)
    {
//        return $model->newQuery()->orderBy('position', 'asc');
        $model = $model->newQuery()->where('is_mixer', 0)->withCount('media_all')->orderBy('position', 'asc');
        if ($this->media_type != null && $this->media_type >= 0) {
            $model = $model->where('type', $this->media_type);
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
        if (\Entrust::can('categories.create') || \Entrust::hasRole('super-admin')) {
            $buttons = ['create'];
        }

        $buttons = array_merge($buttons, [
            'excel',
            'csv',
            'print',
            // 'reset',
            'reload',
        ]);
        // $buttons = array_merge($buttons, [
        //     'csv',
        //     'excel',
        //     'print',
        //     'reset',
        //     'reload',
        // ], Util::getFilterByType());

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
            // 'position'        => ['className' => 'reorder', 'searchable' => false],
            'is_premium'      => ['searchable' => false],
            // 'parent_id'       => ['title' => 'Parent', 'searchable' => false],
            'name'            => ['searchable' => true],
            // 'type'            => ['searchable' => true],
            // 'media_all_count' => ['title' => 'Media Count', 'searchable' => false],
            // 'status'          => ['searchable' => false, 'orderable' => false],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'categoriesdatatable_' . time();
    }
}