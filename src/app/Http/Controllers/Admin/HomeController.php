<?php

namespace App\Http\Controllers\Admin;

use App\Helper\BreadcrumbsRegister;
use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Repositories\Admin\MenuRepository;
use App\Repositories\Admin\RoleRepository;
use App\Repositories\Admin\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class HomeController
 * @package App\Http\Controllers\Admin
 */
class HomeController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var RoleRepository
     */
    protected $roleRepository;

    /**
     * @var MenuRepository
     */
    protected $menuRepository;

    /**
     * HomeController constructor.
     * @param UserRepository $userRepo
     * @param RoleRepository $roleRepo
     * @param MenuRepository $menuRepo
     */
    public function __construct(UserRepository $userRepo, RoleRepository $roleRepo, MenuRepository $menuRepo)
    {
        $this->middleware('auth');
        $this->userRepository = $userRepo;
        $this->roleRepository = $roleRepo;
        $this->menuRepository = $menuRepo;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        $months = null;
//        echo '<script>alert("add authentication pram in API swagger doc")</script>';
        if (App::environment() == 'staging') {
            $this->menuRepository->update(['status' => 0], 5);
        }
        $ordersx = Media::select(DB::raw('MONTHNAME(created_at) as m'), DB::raw('COUNT(*) as Nofo'))->groupBy(DB::raw('MONTHNAME(created_at)'))->whereYear('created_at', '=', date('Y'))->get();
        if (!empty($ordersx)) {
            foreach ($ordersx as $month) {
                $months[]        = $month->m;
                $counts_orders[] = $month->Nofo;
            }
            if (empty($months)) {
                $months[]        = Carbon::now()->format('M');
                $counts_orders[] = 0;
            }
            $months        = array_reverse($months);
            $counts_orders = array_reverse($counts_orders);
        } else {
            $months        = null;
            $counts_orders = null;
        }
        $users = $this->userRepository->findWhereNotIn('id', [1, Auth::id()])->count();
        $roles = $this->roleRepository->all()->count();
        BreadcrumbsRegister::Register();
        return view('admin.home')->with(['users' => $users, 'roles' => $roles, 'counts_orders' => $counts_orders, 'months' => $months]);
    }


}


