<?php

namespace App\DataTables\Admin;

use App\Helper\Util;
use App\Models\Media;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

/**
 * Class MediaDataTable
 * @package App\DataTables\Admin
 */
class MediaDataTable extends DataTable
{

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $query     = $query->with(['user', 'user.details', 'category']);
        $dataTable = new EloquentDataTable($query);

        $dataTable->editColumn('user.details.first_name', function (Media $model) {
            return isset($model->user) && ($model->user->details) ? 
                '<a href="' . route('admin.users.show', $model->user->id) . '" style="font-weight:bold">' . $model->user->details->full_name . '</a>' : 'N/A';
        });

        $dataTable->editColumn('category.name', function (Media $model) {
            return $model->category ? $model->category->title : Util::getNone();
        });

        // $dataTable->editColumn('media_length', function (Media $model) {
        //     return isset($model->media_length) ? $model->media_length : "0";
        // });
        
        $dataTable->editColumn('is_premium', function (Media $model) {
            if ($model->is_premium) {
                return '<span class="label label-success">Yes</span>';
            } else {
                return '<span class="label label-danger">No</span>';
            }
        });
        
        $dataTable->editColumn('image', function (Media $model) {
            return isset($model->image_url) ? '<img src="' . $model->image_url . '" class="bg-black-gradient" style="width:50px;padding:5px;"/>' : "N/A";
        });

//        $dataTable->editColumn('status', function (Media $model) {
//            if (isset($model->deleted_at)) {
//                return '<span class="label label-danger">Inactive</span>';
//            } else return "<span class='label label-success'>Active</span>";
//        });
        $dataTable->addColumn('action', function ($row) {
            return view('admin.medias.datatables_actions', [
                'category' => $row->category,
                'row' => $row,
            ]);
        });
        return $dataTable->rawColumns(['user.details.first_name', 'action', 'is_premium', 'image']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Media $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Media $model)
    {
        $model = $model->newQuery();
        $model = $model->where('is_mixer', 0);
        if ($this->category_id != null && $this->category_id >= 0) {
             $model->where('category_id', $this->category_id);
        }
        if ($this->is_premium != null) {
            $model->where('is_premium', $this->is_premium);
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
        if (\Entrust::can('medias.create') || \Entrust::hasRole('super-admin')) {
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
        //     // 'print',
        //     // 'reset',
        //     'reload',
        // ], Util::getFilterByType());


        if ($this->categories != null) {
            $catButtons = [
                [
                    'name'   => "category_all",
                    'text'   => "<i class='fa fa-check'></i> All",
                    "action" => "function(e, dt, button, config) { window.location.href='" . Util::getUrl(['category_id'], true) . "' }"

                ]
            ];
            foreach ($this->categories as $key => $category) {
                $url          = Util::getUrl(['category_id' => $key]);
                $catButtons[] = [
                    "name"   => "category_" . $key,
                    "text"   => $category,
                    "action" => "function(e, dt, button, config) { window.location.href='" . $url . "' }"
                ];
            }
            $buttons = array_merge($buttons, [
                [
                    "extend"  => "collection",
                    "text"    => "<i class='fa fa-filter'></i> Filter By Category",
                    "buttons" => $catButtons
                ]
            ]);
        }


        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['width' => '120px', 'printable' => false])
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
            'user.details.first_name' => ['title' => 'User', 'orderable' => false, 'searchable' => true],
            'category.name'           => ['title' => 'Category', 'orderable' => false, 'searchable' => false],
            'name'  => ['title' => 'Name', 'orderable' => true, 'searchable' => true],
            'image' => ['title' => 'Image', 'orderable' => false, 'searchable' => false],
            'is_premium' => ['title' => 'Is Premium', 'orderable' => false, 'searchable' => false]
            // 'is_featured',
            // 'status'         => ['orderable' => false, 'searchable' => false],
            // 'media_length'   => ['title' => 'Media Length(s)']
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'mediadatatable_' . time();
    }
}