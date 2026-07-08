<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\UserDataTable;
use App\Helper\BreadcrumbsRegister;
use App\Http\Requests\Admin\CreateUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Repositories\Admin\RoleRepository;
use App\Repositories\Admin\TransactionRepository;
use App\Repositories\Admin\UserDetailRepository;
use App\Repositories\Admin\UserRepository;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Admin\UpdateStatusUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Traits\RequestCacheable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class UserController
 * @package App\Http\Controllers\Admin
 */
class UserController extends AppBaseController
{
    use RequestCacheable;

    /** @var  UserRepository */
    private $userRepository;

    /** ModelName */
    private $ModelName;

    /** ModelName */
    private $BreadCrumbName;

    /** @var  RoleRepository */
    private $roleRepository;

    /** @var  UserDetailRepository */
    private $userDetailRepository;

    public $reqcSuffix = "user";

    public function __construct(UserRepository $userRepo, RoleRepository $roleRepo, UserDetailRepository $detailRepo)
    {
        $this->userRepository       = $userRepo;
        $this->userDetailRepository = $detailRepo;
        $this->roleRepository       = $roleRepo;
        $this->ModelName            = 'users';
        $this->BreadCrumbName       = 'Users';
    }

    /**
     * Display a listing of the User.
     * @param UserDataTable $userDataTable
     * @return Response
     */
    public function index(UserDataTable $userDataTable, Request $request)
    {
        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName);
        $userDataTable->status = $request->get('status', null);
        $userDataTable->subscription = $request->get('subscription', null);
        return $userDataTable->render('admin.users.index', ['title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for creating a new User.
     * @return Response
     */
    public function create()
    {
        // $gender = [1 => 'Male', 2 => 'Female', 3 => 'Non-binary'];
        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName);
        
        $roleIncluded = [Role::ROLE_AUTHENTICATED];
        if(Auth::user()->hasRole('super-admin')){
            array_push($roleIncluded, Role::ROLE_ADMIN);
        }
        $roles = $this->roleRepository->all()->whereIn('id', $roleIncluded)->pluck('display_name', 'id')->all();
        return view('admin.users.create')->with([
            'title'  => $this->BreadCrumbName,
            'roles'  => $roles,
            // 'gender' => $gender
        ]);
    }

    /**
     * Store a newly created User in storage.
     * @param CreateUserRequest $request
     * @return Response
     */
    public function store(CreateUserRequest $request)
    {
        $user = $this->userRepository->saveRecord($request);
        $this->flushCache();

        $this->userDetailRepository->saveRecord($user->id, $request);

        Flash::success('User saved successfully.');
        return redirect(route('admin.users.index'))->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Display the specified User.
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $user = $this->userRepository->findWithoutFail($id);

        if (empty($user)) {
            Flash::error('User not found');
            return redirect(route('admin.users.index'));
        }
        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName, $user);
        return view('admin.users.show')->with(['title' => $this->BreadCrumbName, 'user' => $user]);
    }

    /**
     * Show the form for editing the specified User.
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        $user = $this->userRepository->findWithoutFail($id);
        if (empty($user)) {
            Flash::error('User not found');
            return redirect(route('admin.users.index'));
        }
        
        if($user->hasRole('super-admin') && !\Auth::user()->hasRole('super-admin')) {
            Flash::error('You are not allowed to update this user.');
            return redirect(route('admin.users.index'));
        }
        
        if($user->hasRole('admin') && !\Auth::user()->hasRole(['super-admin', 'admin'])) {
            Flash::error('You are not allowed to update this user.');
            return redirect(route('admin.users.index'));
        }
        $roles  = $this->roleRepository->all()->where('id', '!=', '1')->pluck('display_name', 'id')->all();
        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName, $user);
        return view('admin.users.edit')->with(['user' => $user, 'title' => $this->BreadCrumbName, 'roles' => $roles]);
    }

    /**
     * Update the specified User in storage.
     * @param  int $id
     * @param UpdateUserRequest $request
     * @return Response
     */
    public function update($id, UpdateUserRequest $request)
    {
        $user = $this->userRepository->findWithoutFail($id);
        if (empty($user)) {
            Flash::error('User not found');
            return redirect(route('admin.users.index'));
        }
        
        if($user->hasRole('super-admin') && !\Auth::user()->hasRole('super-admin')) {
            Flash::error('You are not allowed to update this user.');
            return redirect(route('admin.users.index'));
        }
        
        if($user->hasRole('admin') && !\Auth::user()->hasRole(['super-admin', 'admin'])) {
            Flash::error('You are not allowed to update this user.');
            return redirect(route('admin.users.index'));
        }

        $this->userRepository->updateRecord($request, $user);
        $this->userDetailRepository->updateRecord($user->id, $request);
        $this->flushCache();

        $currentUrl = url()->previous();
        if (strpos($currentUrl, 'user/profile') !== false) {
            Flash::success('Profile updated successfully.');
            return redirect(route('admin.users.profile'))->with(['title' => $this->BreadCrumbName]);
        }
        
        Flash::success('User updated successfully.');
        // return redirect()->back();
        return redirect(route('admin.users.index'))->with(['title' => $this->BreadCrumbName]);
    }
    
    public function status($id, UpdateStatusUserRequest $request)
    {
        try{
            $user = User::find($id);

            if (!$user) {
                Flash::error($this->BreadCrumbName . ' not found');
                return redirect(route('admin.users.index'));
            }

            $statusUpdated = DB::table('users')->whereId($id)->update(['status' => $request->status]);

            if($statusUpdated){
                return redirect()->back()->with(['message' => $this->BreadCrumbName . ' status updated successfully.']);
            }else{
                throw new \Error('Something went wrong!');
            }

        } catch (\Error $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }

    /**
     * Remove the specified User from storage.
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        $user = $this->userRepository->findWithoutFail($id);
        if (empty($user)) {
            Flash::error('User not found');
            return redirect(route('admin.users.index'));
        }
        app(TransactionRepository::class)->where('user_id', $user->id)->delete();
        $this->userRepository->deleteRecord($id);
       

        Flash::success('User deleted successfully.');
        return redirect(route('admin.users.index'))->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function profile()
    {
        $user = Auth::user();
        if (empty($user)) {
            Flash::error('User not found');
            return redirect(route('admin.users.index'));
        }
        $this->BreadCrumbName = 'Profile';

        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName);

        return view('admin.users.edit')->with(['title' => $this->BreadCrumbName, 'user' => $user]);
    }


}