<?php

namespace App\Http\Controllers\Admin;

use App\Criteria\CategoryCriteria;
use App\Criteria\PlaylistCriteria;
use App\Criteria\UserCriteria;
use App\Helper\BreadcrumbsRegister;
use App\DataTables\Admin\PlaylistDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreatePlaylistRequest;
use App\Http\Requests\Admin\UpdatePlaylistRequest;
use App\Models\Role;
use App\Repositories\Admin\CategoryRepository;
use App\Repositories\Admin\PlaylistRepository;
use App\Http\Controllers\AppBaseController;
use App\Repositories\Admin\UserRepository;
use App\Traits\RequestCacheable;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Illuminate\Http\Response;

class PlaylistController extends AppBaseController
{
    use RequestCacheable;

    /** ModelName */
    private $ModelName;

    /** BreadCrumbName */
    private $BreadCrumbName;

    /** @var  PlaylistRepository */
    private $playlistRepository;

    /** @var  UserRepository */
    private $userRepository;

    /** @var CategoryRepository */
    private $categoryRepository;

    public $reqcSuffix = "playlist";

    public function __construct(PlaylistRepository $playlistRepo, UserRepository $userRepository, CategoryRepository $categoryRepository)
    {
        $this->playlistRepository = $playlistRepo;
        $this->userRepository     = $userRepository;
        $this->categoryRepository = $categoryRepository;
        $this->ModelName          = 'playlists';
        $this->BreadCrumbName     = 'Playlists';
    }

    /**
     * Display a listing of the Playlist.
     *
     * @param PlaylistDataTable $playlistDataTable
     * @return Response
     */
    public function index(PlaylistDataTable $playlistDataTable, Request $request)
    {
        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName);
        $playlistDataTable->media_type  = $request->get('type', null);
        $playlistDataTable->category_id = $request->get('category_id', null);
        if ($request->get('type', -1) >= 0) {
            $playlistDataTable->categories = $this->categoryRepository->pushCriteria(new CategoryCriteria([
                'type' => $request->get('type')
            ]))->all()->pluck('name', 'id');
        }
        return $playlistDataTable->render('admin.playlists.index', ['title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for creating a new Playlist.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName);
        $this->userRepository->resetCriteria();
        $this->userRepository->pushCriteria(new UserCriteria([
//            'role' => Role::ROLE_ARTIST
        ]));
        $users      = $this->userRepository->all();
        $parent     = $request->get('parent_id', null);
        $categories = [];
        $playlists  = [];
        $playlist   = null;
        if ($parent != null) {
            $playlist = $this->playlistRepository->findWithoutFail($parent);
            if ($playlist) {
                $playlists  = [$playlist->id => $playlist->name];
                $categories = [$playlist->category_id => $playlist->category->name];
            }
        }
        return view('admin.playlists.create')->with([
            'title'      => $this->BreadCrumbName,
            'users'      => $users->pluck('details.full_name', 'id')->toArray(),
            'categories' => $categories,
            'playlists'  => $playlists,
            'parent'     => $playlist
        ]);
    }

    /**
     * Store a newly created Playlist in storage.
     *
     * @param CreatePlaylistRequest $request
     *
     * @return Response
     */
    public function store(CreatePlaylistRequest $request)
    {
        $playlist = $this->playlistRepository->saveRecord($request);
        $this->flushCache();

        Flash::success($this->BreadCrumbName . ' saved successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.playlists.create'));
//            $redirect_to = redirect()->back(201);
        } elseif (isset($request->translation)) {
            $redirect_to = redirect(route('admin.playlists.edit', $playlist->id));
        } else {
            $redirect_to = redirect(route('admin.playlists.index'));
        }
        return $redirect_to->with([
            'title' => $this->BreadCrumbName
        ]);
    }

    /**
     * Display the specified Playlist.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id, PlaylistDataTable $playlistDataTable)
    {
        $playlist = $this->playlistRepository->findWithoutFail($id);

        if (empty($playlist)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.playlists.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName, $playlist);

        $playlistDataTable->parent_id = $id;

//        return view('admin.playlists.show')->with(['playlist' => $playlist, 'title' => $this->BreadCrumbName]);
        return $playlistDataTable->render('admin.playlists.show', ['playlist' => $playlist, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for editing the specified Playlist.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $playlist = $this->playlistRepository->findWithoutFail($id);

        if (empty($playlist)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.playlists.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName, $playlist);

        $this->userRepository->resetCriteria();
        $this->userRepository->pushCriteria(new UserCriteria([
//            'role' => Role::ROLE_ARTIST
        ]));
        $users     = $this->userRepository->all();
        $playlists = [];
        if ($playlist->parent) {
            $playlists = [$playlist->parent_id => $playlist->parent->name];
        }


        return view('admin.playlists.edit')->with([
            'playlist'   => $playlist,
            'parent'     => $playlist->parent,
            'title'      => $this->BreadCrumbName,
            'users'      => $users->pluck('details.full_name', 'id')->toArray(),
            'categories' => [$playlist->category_id => $playlist->category->name,],
            'playlists'  => $playlists
        ]);
    }

    /**
     * Update the specified Playlist in storage.
     *
     * @param  int $id
     * @param UpdatePlaylistRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePlaylistRequest $request)
    {
        $playlist = $this->playlistRepository->findWithoutFail($id);

        if (empty($playlist)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.playlists.index'));
        }

        $playlist = $this->playlistRepository->updateRecord($request, $playlist);
        $this->flushCache();

        Flash::success($this->BreadCrumbName . ' updated successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.playlists.create'));
        } else {
            if ($playlist->parent) {
                $redirect_to = redirect(route('admin.playlists.show', ['id' => $playlist->id]));
            } else {
                $redirect_to = redirect(route('admin.playlists.index'));
            }
        }
        return $redirect_to->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Remove the specified Playlist from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $playlist = $this->playlistRepository->findWithoutFail($id);

        if (empty($playlist)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.playlists.index'));
        }

        $this->playlistRepository->deleteRecord($id);

        Flash::success($this->BreadCrumbName . ' deleted successfully.');
        return redirect(route('admin.playlists.index'))->with(['title' => $this->BreadCrumbName]);
    }

    public function categories(Request $request)
    {
        return ['data' => array_merge([['id' => -1, 'name' => 'None']], $this->categoryRepository->pushCriteria(new CategoryCriteria([
            'type' => $request->get('depends')
        ]))->all()->toArray())];
    }

    public function playlists(Request $request)
    {
        return ['data' => array_merge([['id' => -1, 'name' => 'None']], $this->playlistRepository->pushCriteria(new PlaylistCriteria([
            'type' => $request->get('depends')
        ]))->all()->toArray())];
    }
}
