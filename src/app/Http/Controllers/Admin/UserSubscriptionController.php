<?php

namespace App\Http\Controllers\Admin;

use App\Helper\BreadcrumbsRegister;
use App\DataTables\Admin\UserSubscriptionDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreateUserSubscriptionRequest;
use App\Http\Requests\Admin\UpdateUserSubscriptionRequest;
use App\Repositories\Admin\UserSubscriptionRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Illuminate\Http\Response;

class UserSubscriptionController extends AppBaseController
{
    /** ModelName */
    private $ModelName;

    /** BreadCrumbName */
    private $BreadCrumbName;

    /** @var  UserSubscriptionRepository */
    private $userSubscriptionRepository;

    public function __construct(UserSubscriptionRepository $userSubscriptionRepo)
    {
        $this->userSubscriptionRepository = $userSubscriptionRepo;
        $this->ModelName = 'user-subscriptions';
        $this->BreadCrumbName = 'User Subscriptions';
    }

    /**
     * Display a listing of the UserSubscription.
     *
     * @param UserSubscriptionDataTable $userSubscriptionDataTable
     * @return Response
     */
    public function index(UserSubscriptionDataTable $userSubscriptionDataTable, Request $request)
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        $userSubscriptionDataTable->status = $request->get('status', null);
        $userSubscriptionDataTable->subscription_name = $request->get('subscription_name', null);
        return $userSubscriptionDataTable->render('admin.user_subscriptions.index', ['title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for creating a new UserSubscription.
     *
     * @return Response
     */
    public function create()
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return view('admin.user_subscriptions.create')->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Store a newly created UserSubscription in storage.
     *
     * @param CreateUserSubscriptionRequest $request
     *
     * @return Response
     */
    public function store(CreateUserSubscriptionRequest $request)
    {
        $userSubscription = $this->userSubscriptionRepository->saveRecord($request);

        Flash::success($this->BreadCrumbName . ' saved successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.user-subscriptions.create'));
        } elseif (isset($request->translation)) {
            $redirect_to = redirect(route('admin.user-subscriptions.edit', $userSubscription->id));
        } else {
            $redirect_to = redirect(route('admin.user-subscriptions.index'));
        }
        return $redirect_to->with([
            'title' => $this->BreadCrumbName
        ]);
    }

    /**
     * Display the specified UserSubscription.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $userSubscription = $this->userSubscriptionRepository->with('transactions')->findWithoutFail($id);

        if (empty($userSubscription)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.user-subscriptions.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $userSubscription);
        return view('admin.user_subscriptions.show')->with(['userSubscription' => $userSubscription, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for editing the specified UserSubscription.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $userSubscription = $this->userSubscriptionRepository->findWithoutFail($id);

        if (empty($userSubscription)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.user-subscriptions.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $userSubscription);
        return view('admin.user_subscriptions.edit')->with(['userSubscription' => $userSubscription, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Update the specified UserSubscription in storage.
     *
     * @param  int              $id
     * @param UpdateUserSubscriptionRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUserSubscriptionRequest $request)
    {
        $userSubscription = $this->userSubscriptionRepository->findWithoutFail($id);

        if (empty($userSubscription)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.user-subscriptions.index'));
        }

        $userSubscription = $this->userSubscriptionRepository->updateRecord($request, $userSubscription);

        Flash::success($this->BreadCrumbName . ' updated successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.user-subscriptions.create'));
        } else {
            $redirect_to = redirect(route('admin.user-subscriptions.index'));
        }
        return $redirect_to->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Remove the specified UserSubscription from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $userSubscription = $this->userSubscriptionRepository->findWithoutFail($id);

        if (empty($userSubscription)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.user-subscriptions.index'));
        }

        $this->userSubscriptionRepository->deleteRecord($id);

        Flash::success($this->BreadCrumbName . ' deleted successfully.');
        return redirect(route('admin.user-subscriptions.index'))->with(['title' => $this->BreadCrumbName]);
    }
}
