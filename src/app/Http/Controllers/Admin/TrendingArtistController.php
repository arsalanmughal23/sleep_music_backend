<?php

namespace App\Http\Controllers\Admin;

use App\Helper\BreadcrumbsRegister;
use App\DataTables\Admin\TrendingArtistDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreateTrendingArtistRequest;
use App\Http\Requests\Admin\UpdateTrendingArtistRequest;
use App\Repositories\Admin\TrendingArtistRepository;
use App\Http\Controllers\AppBaseController;
use Laracasts\Flash\Flash;
use Illuminate\Http\Response;

class TrendingArtistController extends AppBaseController
{
    /** ModelName */
    private $ModelName;

    /** BreadCrumbName */
    private $BreadCrumbName;

    /** @var  TrendingArtistRepository */
    private $trendingArtistRepository;

    public function __construct(TrendingArtistRepository $trendingArtistRepo)
    {
        $this->trendingArtistRepository = $trendingArtistRepo;
        $this->ModelName = 'trending-artists';
        $this->BreadCrumbName = 'Trending Artists';
    }

    /**
     * Display a listing of the TrendingArtist.
     *
     * @param TrendingArtistDataTable $trendingArtistDataTable
     * @return Response
     */
    public function index(TrendingArtistDataTable $trendingArtistDataTable)
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return $trendingArtistDataTable->render('admin.trending_artists.index', ['title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for creating a new TrendingArtist.
     *
     * @return Response
     */
    public function create()
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return view('admin.trending_artists.create')->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Store a newly created TrendingArtist in storage.
     *
     * @param CreateTrendingArtistRequest $request
     *
     * @return Response
     */
    public function store(CreateTrendingArtistRequest $request)
    {
        $trendingArtist = $this->trendingArtistRepository->saveRecord($request);

        Flash::success($this->BreadCrumbName . ' saved successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.trending-artists.create'));
        } elseif (isset($request->translation)) {
            $redirect_to = redirect(route('admin.trending-artists.edit', $trendingArtist->id));
        } else {
            $redirect_to = redirect(route('admin.trending-artists.index'));
        }
        return $redirect_to->with([
            'title' => $this->BreadCrumbName
        ]);
    }

    /**
     * Display the specified TrendingArtist.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $trendingArtist = $this->trendingArtistRepository->findWithoutFail($id);

        if (empty($trendingArtist)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.trending-artists.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $trendingArtist);
        return view('admin.trending_artists.show')->with(['trendingArtist' => $trendingArtist, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for editing the specified TrendingArtist.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $trendingArtist = $this->trendingArtistRepository->findWithoutFail($id);

        if (empty($trendingArtist)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.trending-artists.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $trendingArtist);
        return view('admin.trending_artists.edit')->with(['trendingArtist' => $trendingArtist, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Update the specified TrendingArtist in storage.
     *
     * @param  int              $id
     * @param UpdateTrendingArtistRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTrendingArtistRequest $request)
    {
        $trendingArtist = $this->trendingArtistRepository->findWithoutFail($id);

        if (empty($trendingArtist)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.trending-artists.index'));
        }

        $trendingArtist = $this->trendingArtistRepository->updateRecord($request, $trendingArtist);

        Flash::success($this->BreadCrumbName . ' updated successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.trending-artists.create'));
        } else {
            $redirect_to = redirect(route('admin.trending-artists.index'));
        }
        return $redirect_to->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Remove the specified TrendingArtist from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $trendingArtist = $this->trendingArtistRepository->findWithoutFail($id);

        if (empty($trendingArtist)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.trending-artists.index'));
        }

        $this->trendingArtistRepository->deleteRecord($id);

        Flash::success($this->BreadCrumbName . ' deleted successfully.');
        return redirect(route('admin.trending-artists.index'))->with(['title' => $this->BreadCrumbName]);
    }
}
