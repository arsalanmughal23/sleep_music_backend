<?php

namespace App\DataTables\Admin;

use App\Helper\Util;
use App\Models\Category;
use App\Models\Playlist;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

/**
 * Class PlaylistDataTable
 * @package App\DataTables\Admin
 */
class PlaylistDataTable extends DataTable
{
    public $media_type  = null;
    public $category_id = null;
    public $categories  = null;
    public $parent_id   = null;

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);


        $dataTable->editColumn('user_id', function (Playlist $model) {
            return $model->user ? $model->user->name : Util::getNone();
        });
        $dataTable->editColumn('image', function (Playlist $model) {
            return '<img src="' . $model->image_url . '" style="max-width: 150px" />';
        });
        $dataTable->editColumn('type', function (Playlist $model) {
            return $model->type_text;
        });
        $dataTable->editColumn('category.name', function (Playlist $model) {
            return $model->category ? $model->category->name : Util::getNone();
        });

        /*$dataTable->editColumn('parent.name', function (Playlist $model) {
            return $model->parent ? $model->parent->name : Util::getNone();
        });*/

        $dataTable->editColumn('is_featured', function (Playlist $model) {
            return '<span class="label label-' . Util::getBoolCss($model->is_featured) . '">' . Util::getBoolText($model->is_featured) . '</span>';
        });

        $dataTable->editColumn('is_protected', function (Playlist $model) {
            return '<span class="label label-' . Util::getBoolCss($model->is_protected) . '">' . Util::getBoolText($model->is_protected) . '</span>';
        });

        $dataTable->rawColumns(['user_id', 'category.name', 'parent.name', 'image', 'is_featured', 'is_protected', 'action']);

        return $dataTable->addColumn('action', 'admin.playlists.datatables_actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Playlist $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Playlist $model)
    {
//        return $model->newQuery();
        $model = $model->newQuery()->where('parent_id', $this->parent_id)->with(['category']);
        if ($this->media_type != null && $this->media_type >= 0) {
            $model = $model->where('type', $this->media_type);
        }

        if ($this->category_id != null && $this->category_id >= 0) {
            $model = $model->where('category_id', $this->category_id);
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
        if (\Entrust::can('playlists.create') || \Entrust::hasRole('super-admin')) {
            $buttons = ['create'];
        }
        if ($this->parent_id != null) {
            $buttons = array_merge([
                [
                    "name"   => "create_child",
                    "text"   => "<i class='fa fa-plus'></i> Create",
                    "action" => "function(e, dt, button, config) { window.location.href='" . route('admin.playlists.create', ['parent_id' => $this->parent_id]) . "' }"
                ],
                'export',
                'print',
                'reset',
                'reload'
            ]);
        } else {
            $buttons = array_merge($buttons, [
                'export',
                'print',
                'reset',
                'reload'
            ], Util::getFilterByType());

            if ($this->categories != null) {
                $catButtons = [];
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
        }


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
        if ($this->parent_id != null || $this->parent_id > 0) {
            return [
                'user_id' => [
                    'title'      => 'User',
                    'searchable' => false
                ],
                'name',
                'image'   => [
                    'searchable' => false,
                ],
                'is_featured',
                'is_protected',
            ];
        }
        return [
            'user_id'       => [
                'title'      => 'User',
                'searchable' => false
            ],
            'name',
            'image'         => [
                'searchable' => false,
            ],
            'type',
            'category.name' => [
                'title' => 'Category'
            ],
            /*'parent.name'   => [
                'title' => 'Parent'
            ],*/
            'is_featured',
            'is_protected',
//            'sort_key'
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'playlistsdatatable_' . time();
    }
}