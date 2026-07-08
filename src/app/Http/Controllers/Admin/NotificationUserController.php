<?php

namespace App\Http\Controllers\Admin;

use App\Helper\BreadcrumbsRegister;
use App\DataTables\Admin\NotificationUserDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreateNotificationUserRequest;
use App\Http\Requests\Admin\UpdateNotificationUserRequest;
use App\Repositories\Admin\NotificationUserRepository;
use App\Http\Controllers\AppBaseController;
use Laracasts\Flash\Flash;
use Illuminate\Http\Response;

class NotificationUserController extends AppBaseController
{
    /** ModelName */
    private $ModelName;

    /** BreadCrumbName */
    private $BreadCrumbName;

    /** @var  NotificationUserRepository */
    private $notificationUserRepository;

    public function __construct(NotificationUserRepository $notificationUserRepo)
    {
        $this->notificationUserRepository = $notificationUserRepo;
        $this->ModelName = 'notification-users';
        $this->BreadCrumbName = 'Notification Users';
    }

    /**
     * Display a listing of the NotificationUser.
     *
     * @param NotificationUserDataTable $notificationUserDataTable
     * @return Response
     */
    public function index(NotificationUserDataTable $notificationUserDataTable)
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return $notificationUserDataTable->render('admin.notification_users.index', ['title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for creating a new NotificationUser.
     *
     * @return Response
     */
    public function create()
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return view('admin.notification_users.create')->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Store a newly created NotificationUser in storage.
     *
     * @param CreateNotificationUserRequest $request
     *
     * @return Response
     */
    public function store(CreateNotificationUserRequest $request)
    {
        $notificationUser = $this->notificationUserRepository->saveRecord($request);

        Flash::success($this->BreadCrumbName . ' saved successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.notification-users.create'));
        } elseif (isset($request->translation)) {
            $redirect_to = redirect(route('admin.notification-users.edit', $notificationUser->id));
        } else {
            $redirect_to = redirect(route('admin.notification-users.index'));
        }
        return $redirect_to->with([
            'title' => $this->BreadCrumbName
        ]);
    }

    /**
     * Display the specified NotificationUser.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $notificationUser = $this->notificationUserRepository->findWithoutFail($id);

        if (empty($notificationUser)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.notification-users.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $notificationUser);
        return view('admin.notification_users.show')->with(['notificationUser' => $notificationUser, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for editing the specified NotificationUser.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $notificationUser = $this->notificationUserRepository->findWithoutFail($id);

        if (empty($notificationUser)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.notification-users.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $notificationUser);
        return view('admin.notification_users.edit')->with(['notificationUser' => $notificationUser, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Update the specified NotificationUser in storage.
     *
     * @param  int              $id
     * @param UpdateNotificationUserRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateNotificationUserRequest $request)
    {
        $notificationUser = $this->notificationUserRepository->findWithoutFail($id);

        if (empty($notificationUser)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.notification-users.index'));
        }

        $notificationUser = $this->notificationUserRepository->updateRecord($request, $notificationUser);

        Flash::success($this->BreadCrumbName . ' updated successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.notification-users.create'));
        } else {
            $redirect_to = redirect(route('admin.notification-users.index'));
        }
        return $redirect_to->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Remove the specified NotificationUser from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $notificationUser = $this->notificationUserRepository->findWithoutFail($id);

        if (empty($notificationUser)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.notification-users.index'));
        }

        $this->notificationUserRepository->deleteRecord($id);

        Flash::success($this->BreadCrumbName . ' deleted successfully.');
        return redirect(route('admin.notification-users.index'))->with(['title' => $this->BreadCrumbName]);
    }
}
