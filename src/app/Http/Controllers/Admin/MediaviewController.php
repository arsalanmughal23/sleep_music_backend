<?php

namespace App\Http\Controllers\Admin;

use App\Helper\BreadcrumbsRegister;
use App\DataTables\Admin\MediaviewDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreateMediaviewRequest;
use App\Http\Requests\Admin\UpdateMediaviewRequest;
use App\Repositories\Admin\MediaviewRepository;
use App\Http\Controllers\AppBaseController;
use Laracasts\Flash\Flash;
use Illuminate\Http\Response;

class MediaviewController extends AppBaseController
{
    /** ModelName */
    private $ModelName;

    /** BreadCrumbName */
    private $BreadCrumbName;

    /** @var  MediaviewRepository */
    private $mediaviewRepository;

    public function __construct(MediaviewRepository $mediaviewRepo)
    {
        $this->mediaviewRepository = $mediaviewRepo;
        $this->ModelName = 'mediaviews';
        $this->BreadCrumbName = 'Mediaviews';
    }

    /**
     * Display a listing of the Mediaview.
     *
     * @param MediaviewDataTable $mediaviewDataTable
     * @return Response
     */
    public function index(MediaviewDataTable $mediaviewDataTable)
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return $mediaviewDataTable->render('admin.mediaviews.index', ['title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for creating a new Mediaview.
     *
     * @return Response
     */
    public function create()
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return view('admin.mediaviews.create')->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Store a newly created Mediaview in storage.
     *
     * @param CreateMediaviewRequest $request
     *
     * @return Response
     */
    public function store(CreateMediaviewRequest $request)
    {
        $mediaview = $this->mediaviewRepository->saveRecord($request);

        Flash::success($this->BreadCrumbName . ' saved successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.mediaviews.create'));
        } elseif (isset($request->translation)) {
            $redirect_to = redirect(route('admin.mediaviews.edit', $mediaview->id));
        } else {
            $redirect_to = redirect(route('admin.mediaviews.index'));
        }
        return $redirect_to->with([
            'title' => $this->BreadCrumbName
        ]);
    }

    /**
     * Display the specified Mediaview.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $mediaview = $this->mediaviewRepository->findWithoutFail($id);

        if (empty($mediaview)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.mediaviews.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $mediaview);
        return view('admin.mediaviews.show')->with(['mediaview' => $mediaview, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for editing the specified Mediaview.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $mediaview = $this->mediaviewRepository->findWithoutFail($id);

        if (empty($mediaview)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.mediaviews.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $mediaview);
        return view('admin.mediaviews.edit')->with(['mediaview' => $mediaview, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Update the specified Mediaview in storage.
     *
     * @param  int              $id
     * @param UpdateMediaviewRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateMediaviewRequest $request)
    {
        $mediaview = $this->mediaviewRepository->findWithoutFail($id);

        if (empty($mediaview)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.mediaviews.index'));
        }

        $mediaview = $this->mediaviewRepository->updateRecord($request, $mediaview);

        Flash::success($this->BreadCrumbName . ' updated successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.mediaviews.create'));
        } else {
            $redirect_to = redirect(route('admin.mediaviews.index'));
        }
        return $redirect_to->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Remove the specified Mediaview from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $mediaview = $this->mediaviewRepository->findWithoutFail($id);

        if (empty($mediaview)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.mediaviews.index'));
        }

        $this->mediaviewRepository->deleteRecord($id);

        Flash::success($this->BreadCrumbName . ' deleted successfully.');
        return redirect(route('admin.mediaviews.index'))->with(['title' => $this->BreadCrumbName]);
    }
}
