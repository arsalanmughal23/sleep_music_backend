<?php

namespace App\Http\Controllers\Admin;

use App\Helper\BreadcrumbsRegister;
use App\DataTables\Admin\LikeDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreateLikeRequest;
use App\Http\Requests\Admin\UpdateLikeRequest;
use App\Repositories\Admin\LikeRepository;
use App\Http\Controllers\AppBaseController;
use Laracasts\Flash\Flash;
use Illuminate\Http\Response;

class LikeController extends AppBaseController
{
    /** ModelName */
    private $ModelName;

    /** BreadCrumbName */
    private $BreadCrumbName;

    /** @var  LikeRepository */
    private $likeRepository;

    public function __construct(LikeRepository $likeRepo)
    {
        $this->likeRepository = $likeRepo;
        $this->ModelName = 'likes';
        $this->BreadCrumbName = 'Likes';
    }

    /**
     * Display a listing of the Like.
     *
     * @param LikeDataTable $likeDataTable
     * @return Response
     */
    public function index(LikeDataTable $likeDataTable)
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return $likeDataTable->render('admin.likes.index', ['title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for creating a new Like.
     *
     * @return Response
     */
    public function create()
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return view('admin.likes.create')->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Store a newly created Like in storage.
     *
     * @param CreateLikeRequest $request
     *
     * @return Response
     */
    public function store(CreateLikeRequest $request)
    {
        $like = $this->likeRepository->saveRecord($request);

        Flash::success($this->BreadCrumbName . ' saved successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.likes.create'));
        } elseif (isset($request->translation)) {
            $redirect_to = redirect(route('admin.likes.edit', $like->id));
        } else {
            $redirect_to = redirect(route('admin.likes.index'));
        }
        return $redirect_to->with([
            'title' => $this->BreadCrumbName
        ]);
    }

    /**
     * Display the specified Like.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $like = $this->likeRepository->findWithoutFail($id);

        if (empty($like)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.likes.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $like);
        return view('admin.likes.show')->with(['like' => $like, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for editing the specified Like.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $like = $this->likeRepository->findWithoutFail($id);

        if (empty($like)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.likes.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $like);
        return view('admin.likes.edit')->with(['like' => $like, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Update the specified Like in storage.
     *
     * @param  int              $id
     * @param UpdateLikeRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateLikeRequest $request)
    {
        $like = $this->likeRepository->findWithoutFail($id);

        if (empty($like)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.likes.index'));
        }

        $like = $this->likeRepository->updateRecord($request, $like);

        Flash::success($this->BreadCrumbName . ' updated successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.likes.create'));
        } else {
            $redirect_to = redirect(route('admin.likes.index'));
        }
        return $redirect_to->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Remove the specified Like from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $like = $this->likeRepository->findWithoutFail($id);

        if (empty($like)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.likes.index'));
        }

        $this->likeRepository->deleteRecord($id);

        Flash::success($this->BreadCrumbName . ' deleted successfully.');
        return redirect(route('admin.likes.index'))->with(['title' => $this->BreadCrumbName]);
    }
}
