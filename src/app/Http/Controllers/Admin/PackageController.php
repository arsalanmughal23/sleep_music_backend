<?php

namespace App\Http\Controllers\Admin;

use App\Helper\BreadcrumbsRegister;
use App\DataTables\Admin\PackageDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreatePackageRequest;
use App\Http\Requests\Admin\UpdatePackageRequest;
use App\Repositories\Admin\PackageRepository;
use App\Http\Controllers\AppBaseController;
use Laracasts\Flash\Flash;
use Illuminate\Http\Response;

class PackageController extends AppBaseController
{
    /** ModelName */
    private $ModelName;

    /** BreadCrumbName */
    private $BreadCrumbName;

    /** @var  PackageRepository */
    private $packageRepository;

    public function __construct(PackageRepository $packageRepo)
    {
        $this->packageRepository = $packageRepo;
        $this->ModelName = 'packages';
        $this->BreadCrumbName = 'Packages';
    }

    /**
     * Display a listing of the Package.
     *
     * @param PackageDataTable $packageDataTable
     * @return Response
     */
    public function index(PackageDataTable $packageDataTable)
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return $packageDataTable->render('admin.packages.index', ['title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for creating a new Package.
     *
     * @return Response
     */
    public function create()
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return view('admin.packages.create')->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Store a newly created Package in storage.
     *
     * @param CreatePackageRequest $request
     *
     * @return Response
     */
    public function store(CreatePackageRequest $request)
    {
        $package = $this->packageRepository->saveRecord($request);

        Flash::success($this->BreadCrumbName . ' saved successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.packages.create'));
        } elseif (isset($request->translation)) {
            $redirect_to = redirect(route('admin.packages.edit', $package->id));
        } else {
            $redirect_to = redirect(route('admin.packages.index'));
        }
        return $redirect_to->with([
            'title' => $this->BreadCrumbName
        ]);
    }

    /**
     * Display the specified Package.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $package = $this->packageRepository->findWithoutFail($id);

        if (empty($package)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.packages.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $package);
        return view('admin.packages.show')->with(['package' => $package, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for editing the specified Package.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $package = $this->packageRepository->findWithoutFail($id);

        if (empty($package)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.packages.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $package);
        return view('admin.packages.edit')->with(['package' => $package, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Update the specified Package in storage.
     *
     * @param  int              $id
     * @param UpdatePackageRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePackageRequest $request)
    {
        $package = $this->packageRepository->findWithoutFail($id);

        if (empty($package)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.packages.index'));
        }

        $package = $this->packageRepository->updateRecord($request, $package);

        Flash::success($this->BreadCrumbName . ' updated successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.packages.create'));
        } else {
            $redirect_to = redirect(route('admin.packages.index'));
        }
        return $redirect_to->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Remove the specified Package from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $package = $this->packageRepository->findWithoutFail($id);

        if (empty($package)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.packages.index'));
        }

        $this->packageRepository->deleteRecord($id);

        Flash::success($this->BreadCrumbName . ' deleted successfully.');
        return redirect(route('admin.packages.index'))->with(['title' => $this->BreadCrumbName]);
    }
}
