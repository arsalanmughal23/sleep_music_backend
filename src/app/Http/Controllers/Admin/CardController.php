<?php

namespace App\Http\Controllers\Admin;

use App\Helper\BreadcrumbsRegister;
use App\DataTables\Admin\CardDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreateCardRequest;
use App\Http\Requests\Admin\UpdateCardRequest;
use App\Repositories\Admin\CardRepository;
use App\Http\Controllers\AppBaseController;
use Laracasts\Flash\Flash;
use Illuminate\Http\Response;

class CardController extends AppBaseController
{
    /** ModelName */
    private $ModelName;

    /** BreadCrumbName */
    private $BreadCrumbName;

    /** @var  CardRepository */
    private $cardRepository;

    public function __construct(CardRepository $cardRepo)
    {
        $this->cardRepository = $cardRepo;
        $this->ModelName = 'cards';
        $this->BreadCrumbName = 'Cards';
    }

    /**
     * Display a listing of the Card.
     *
     * @param CardDataTable $cardDataTable
     * @return Response
     */
    public function index(CardDataTable $cardDataTable)
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return $cardDataTable->render('admin.cards.index', ['title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for creating a new Card.
     *
     * @return Response
     */
    public function create()
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return view('admin.cards.create')->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Store a newly created Card in storage.
     *
     * @param CreateCardRequest $request
     *
     * @return Response
     */
    public function store(CreateCardRequest $request)
    {
        $card = $this->cardRepository->saveRecord($request);

        Flash::success($this->BreadCrumbName . ' saved successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.cards.create'));
        } elseif (isset($request->translation)) {
            $redirect_to = redirect(route('admin.cards.edit', $card->id));
        } else {
            $redirect_to = redirect(route('admin.cards.index'));
        }
        return $redirect_to->with([
            'title' => $this->BreadCrumbName
        ]);
    }

    /**
     * Display the specified Card.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $card = $this->cardRepository->findWithoutFail($id);

        if (empty($card)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.cards.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $card);
        return view('admin.cards.show')->with(['card' => $card, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for editing the specified Card.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $card = $this->cardRepository->findWithoutFail($id);

        if (empty($card)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.cards.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $card);
        return view('admin.cards.edit')->with(['card' => $card, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Update the specified Card in storage.
     *
     * @param  int              $id
     * @param UpdateCardRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCardRequest $request)
    {
        $card = $this->cardRepository->findWithoutFail($id);

        if (empty($card)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.cards.index'));
        }

        $card = $this->cardRepository->updateRecord($request, $card);

        Flash::success($this->BreadCrumbName . ' updated successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.cards.create'));
        } else {
            $redirect_to = redirect(route('admin.cards.index'));
        }
        return $redirect_to->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Remove the specified Card from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $card = $this->cardRepository->findWithoutFail($id);

        if (empty($card)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.cards.index'));
        }

        $this->cardRepository->deleteRecord($id);

        Flash::success($this->BreadCrumbName . ' deleted successfully.');
        return redirect(route('admin.cards.index'))->with(['title' => $this->BreadCrumbName]);
    }
}
