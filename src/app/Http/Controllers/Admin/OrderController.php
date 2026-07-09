<?php

namespace App\Http\Controllers\Admin;

use App\Helper\BreadcrumbsRegister;
use App\DataTables\Admin\OrderDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreateOrderRequest;
use App\Http\Requests\Admin\UpdateOrderRequest;
use App\Repositories\Admin\OrderRepository;
use App\Http\Controllers\AppBaseController;
use Laracasts\Flash\Flash;
use Illuminate\Http\Response;

class OrderController extends AppBaseController
{
    /** ModelName */
    private $ModelName;

    /** BreadCrumbName */
    private $BreadCrumbName;

    /** @var  OrderRepository */
    private $orderRepository;

    public function __construct(OrderRepository $orderRepo)
    {
        $this->orderRepository = $orderRepo;
        $this->ModelName = 'orders';
        $this->BreadCrumbName = 'Orders';
    }

    /**
     * Display a listing of the Order.
     *
     * @param OrderDataTable $orderDataTable
     * @return Response
     */
    public function index(OrderDataTable $orderDataTable)
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return $orderDataTable->render('admin.orders.index', ['title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for creating a new Order.
     *
     * @return Response
     */
    public function create()
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return view('admin.orders.create')->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Store a newly created Order in storage.
     *
     * @param CreateOrderRequest $request
     *
     * @return Response
     */
    public function store(CreateOrderRequest $request)
    {
        $order = $this->orderRepository->saveRecord($request);

        Flash::success($this->BreadCrumbName . ' saved successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.orders.create'));
        } elseif (isset($request->translation)) {
            $redirect_to = redirect(route('admin.orders.edit', $order->id));
        } else {
            $redirect_to = redirect(route('admin.orders.index'));
        }
        return $redirect_to->with([
            'title' => $this->BreadCrumbName
        ]);
    }

    /**
     * Display the specified Order.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $order = $this->orderRepository->findWithoutFail($id);

        if (empty($order)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.orders.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $order);
        return view('admin.orders.show')->with(['order' => $order, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for editing the specified Order.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $order = $this->orderRepository->findWithoutFail($id);

        if (empty($order)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.orders.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $order);
        return view('admin.orders.edit')->with(['order' => $order, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Update the specified Order in storage.
     *
     * @param  int              $id
     * @param UpdateOrderRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateOrderRequest $request)
    {
        $order = $this->orderRepository->findWithoutFail($id);

        if (empty($order)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.orders.index'));
        }

        $order = $this->orderRepository->updateRecord($request, $order);

        Flash::success($this->BreadCrumbName . ' updated successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.orders.create'));
        } else {
            $redirect_to = redirect(route('admin.orders.index'));
        }
        return $redirect_to->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Remove the specified Order from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $order = $this->orderRepository->findWithoutFail($id);

        if (empty($order)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.orders.index'));
        }

        $this->orderRepository->deleteRecord($id);

        Flash::success($this->BreadCrumbName . ' deleted successfully.');
        return redirect(route('admin.orders.index'))->with(['title' => $this->BreadCrumbName]);
    }
}
