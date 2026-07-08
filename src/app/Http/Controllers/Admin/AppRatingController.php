<?php

namespace App\Http\Controllers\Admin;

use App\Helper\BreadcrumbsRegister;
use App\DataTables\Admin\AppRatingDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreateAppRatingRequest;
use App\Http\Requests\Admin\UpdateAppRatingRequest;
use App\Repositories\Admin\AppRatingRepository;
use App\Http\Controllers\AppBaseController;
use Laracasts\Flash\Flash;
use Illuminate\Http\Response;

class AppRatingController extends AppBaseController
{
    /** ModelName */
    private $ModelName;

    /** BreadCrumbName */
    private $BreadCrumbName;

    /** @var  AppRatingRepository */
    private $appRatingRepository;

    public function __construct(AppRatingRepository $appRatingRepo)
    {
        $this->appRatingRepository = $appRatingRepo;
        $this->ModelName = 'app-ratings';
        $this->BreadCrumbName = 'App Ratings';
    }

    /**
     * Display a listing of the AppRating.
     *
     * @param AppRatingDataTable $appRatingDataTable
     * @return Response
     */
    public function index(AppRatingDataTable $appRatingDataTable)
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return $appRatingDataTable->render('admin.app_ratings.index', ['title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for creating a new AppRating.
     *
     * @return Response
     */
    public function create()
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return view('admin.app_ratings.create')->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Store a newly created AppRating in storage.
     *
     * @param CreateAppRatingRequest $request
     *
     * @return Response
     */
    public function store(CreateAppRatingRequest $request)
    {
        $appRating = $this->appRatingRepository->saveRecord($request);

        Flash::success($this->BreadCrumbName . ' saved successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.app-ratings.create'));
        } elseif (isset($request->translation)) {
            $redirect_to = redirect(route('admin.app-ratings.edit', $appRating->id));
        } else {
            $redirect_to = redirect(route('admin.app-ratings.index'));
        }
        return $redirect_to->with([
            'title' => $this->BreadCrumbName
        ]);
    }

    /**
     * Display the specified AppRating.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $appRating = $this->appRatingRepository->findWithoutFail($id);

        if (empty($appRating)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.app-ratings.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $appRating);
        return view('admin.app_ratings.show')->with(['appRating' => $appRating, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for editing the specified AppRating.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $appRating = $this->appRatingRepository->findWithoutFail($id);

        if (empty($appRating)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.app-ratings.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $appRating);
        return view('admin.app_ratings.edit')->with(['appRating' => $appRating, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Update the specified AppRating in storage.
     *
     * @param  int              $id
     * @param UpdateAppRatingRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAppRatingRequest $request)
    {
        $appRating = $this->appRatingRepository->findWithoutFail($id);

        if (empty($appRating)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.app-ratings.index'));
        }

        $appRating = $this->appRatingRepository->updateRecord($request, $appRating);

        Flash::success($this->BreadCrumbName . ' updated successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.app-ratings.create'));
        } else {
            $redirect_to = redirect(route('admin.app-ratings.index'));
        }
        return $redirect_to->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Remove the specified AppRating from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $appRating = $this->appRatingRepository->findWithoutFail($id);

        if (empty($appRating)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.app-ratings.index'));
        }

        $this->appRatingRepository->deleteRecord($id);

        Flash::success($this->BreadCrumbName . ' deleted successfully.');
        return redirect(route('admin.app-ratings.index'))->with(['title' => $this->BreadCrumbName]);
    }
}
