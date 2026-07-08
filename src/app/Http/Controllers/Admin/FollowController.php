<?php

namespace App\Http\Controllers\Admin;

use App\Helper\BreadcrumbsRegister;
use App\DataTables\Admin\FollowDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreateFollowRequest;
use App\Http\Requests\Admin\UpdateFollowRequest;
use App\Repositories\Admin\FollowRepository;
use App\Http\Controllers\AppBaseController;
use Laracasts\Flash\Flash;
use Illuminate\Http\Response;

class FollowController extends AppBaseController
{
    /** ModelName */
    private $ModelName;

    /** BreadCrumbName */
    private $BreadCrumbName;

    /** @var  FollowRepository */
    private $followRepository;

    public function __construct(FollowRepository $followRepo)
    {
        $this->followRepository = $followRepo;
        $this->ModelName = 'follows';
        $this->BreadCrumbName = 'Follows';
    }

    /**
     * Display a listing of the Follow.
     *
     * @param FollowDataTable $followDataTable
     * @return Response
     */
    public function index(FollowDataTable $followDataTable)
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return $followDataTable->render('admin.follows.index', ['title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for creating a new Follow.
     *
     * @return Response
     */
    public function create()
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return view('admin.follows.create')->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Store a newly created Follow in storage.
     *
     * @param CreateFollowRequest $request
     *
     * @return Response
     */
    public function store(CreateFollowRequest $request)
    {
        $follow = $this->followRepository->saveRecord($request);

        Flash::success($this->BreadCrumbName . ' saved successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.follows.create'));
        } elseif (isset($request->translation)) {
            $redirect_to = redirect(route('admin.follows.edit', $follow->id));
        } else {
            $redirect_to = redirect(route('admin.follows.index'));
        }
        return $redirect_to->with([
            'title' => $this->BreadCrumbName
        ]);
    }

    /**
     * Display the specified Follow.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $follow = $this->followRepository->findWithoutFail($id);

        if (empty($follow)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.follows.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $follow);
        return view('admin.follows.show')->with(['follow' => $follow, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for editing the specified Follow.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $follow = $this->followRepository->findWithoutFail($id);

        if (empty($follow)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.follows.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $follow);
        return view('admin.follows.edit')->with(['follow' => $follow, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Update the specified Follow in storage.
     *
     * @param  int              $id
     * @param UpdateFollowRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFollowRequest $request)
    {
        $follow = $this->followRepository->findWithoutFail($id);

        if (empty($follow)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.follows.index'));
        }

        $follow = $this->followRepository->updateRecord($request, $follow);

        Flash::success($this->BreadCrumbName . ' updated successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.follows.create'));
        } else {
            $redirect_to = redirect(route('admin.follows.index'));
        }
        return $redirect_to->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Remove the specified Follow from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $follow = $this->followRepository->findWithoutFail($id);

        if (empty($follow)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.follows.index'));
        }

        $this->followRepository->deleteRecord($id);

        Flash::success($this->BreadCrumbName . ' deleted successfully.');
        return redirect(route('admin.follows.index'))->with(['title' => $this->BreadCrumbName]);
    }
}
