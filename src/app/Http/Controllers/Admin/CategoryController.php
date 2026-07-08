<?php

namespace App\Http\Controllers\Admin;

use App\Criteria\CategoryCriteria;
use App\DataTables\Admin\MediaDataTable;
use App\Helper\BreadcrumbsRegister;
use App\DataTables\Admin\CategoryDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreateCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Repositories\Admin\CategoryRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\Category;
use App\Traits\RequestCacheable;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Illuminate\Http\Response;

class CategoryController extends AppBaseController
{
    use RequestCacheable;

    /** ModelName */
    private $ModelName;

    /** BreadCrumbName */
    private $BreadCrumbName;

    /** @var  CategoryRepository */
    private $categoryRepository;

    public $reqcSuffix = "category";

    public function __construct(CategoryRepository $categoryRepo)
    {
        $this->categoryRepository = $categoryRepo;
        $this->ModelName          = 'categories';
        $this->BreadCrumbName     = 'Categories';
    }

    /**
     * Display a listing of the Category.
     *
     * @param CategoryDataTable $categoryDataTable
     * @return Response
     */
    public function index(CategoryDataTable $categoryDataTable, Request $request)
    {
        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName);
        $categoryDataTable->media_type = $request->get('type', null);

        return $categoryDataTable->render('admin.categories.index', ['title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for creating a new Category.
     *
     * @return Response
     */
    public function create()
    {
        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName);

        $this->categoryRepository->resetCriteria();
        $this->categoryRepository->pushCriteria(new CategoryCriteria([
            'root' => true
        ]));
        $root = $this->categoryRepository->all();
        return view('admin.categories.create')->with(['title' => $this->BreadCrumbName, 'root' => $root->pluck('name', 'id')->toArray()]);
    }

    /**
     * Store a newly created Category in storage.
     *
     * @param CreateCategoryRequest $request
     *
     * @return Response
     */
    public function store(CreateCategoryRequest $request)
    {
        if ($request->has('parent_id') && $request->parent_id != 0) {
            if(!Category::find($request->parent_id)){
                return redirect()->back()->withInputs()->withErrors(['Something went wrong!']);
            }
        }

        $category = $this->categoryRepository->saveRecord($request);
        $this->flushCache();

        Flash::success($this->BreadCrumbName . ' saved successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.categories.create'));
        } elseif (isset($request->translation)) {
            $redirect_to = redirect(route('admin.categories.edit', $category->id));
        } else {
            $redirect_to = redirect(route('admin.categories.index'));
        }
        return $redirect_to->with([
            'title' => $this->BreadCrumbName
        ]);
    }

    /**
     * Display the specified Category.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id, MediaDataTable $mediaDataTable)
    {
        $category = $this->categoryRepository->findWithoutFail($id);

        if (empty($category)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.categories.index'));
        }
        $mediaDataTable->category_id = $id;

        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName, $category);
        return $mediaDataTable->render('admin.categories.show', ['category' => $category, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for editing the specified Category.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $category = $this->categoryRepository->findWithoutFail($id);

        if (empty($category)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.categories.index'));
        }

        $this->categoryRepository->resetCriteria();
        $this->categoryRepository->pushCriteria(new CategoryCriteria([
            'root' => true
        ]));
        $root = $this->categoryRepository->all();
        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName, $category);
        return view('admin.categories.edit')->with(['category' => $category, 'title' => $this->BreadCrumbName, 'root' => $root->pluck('name', 'id')->toArray()]);
    }

    /**
     * Update the specified Category in storage.
     *
     * @param int $id
     * @param UpdateCategoryRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCategoryRequest $request)
    {
        $category = $this->categoryRepository->findWithoutFail($id);

        if (empty($category)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.categories.index'));
        }

        if ($request->has('parent_id') && $request->parent_id != 0) {
            if(!Category::find($request->parent_id)){
                return redirect()->back()->withInput()->withErrors(['Something went wrong!']);
            }
        }

        $category = $this->categoryRepository->updateRecord($request, $category);
        $this->flushCache();

        Flash::success($this->BreadCrumbName . ' updated successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.categories.create'));
        } else {
            $redirect_to = redirect(route('admin.categories.index'));
        }
        return $redirect_to->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Remove the specified Category from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $category = $this->categoryRepository->findWithoutFail($id);

        if (empty($category)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.categories.index'));
        }

        $this->categoryRepository->deleteRecord($id);

        Flash::success('Category deleted successfully.');
        return redirect(route('admin.categories.index'))->with(['title' => $this->BreadCrumbName]);
    }


    public function swape(Request $request)
    {
        $input = $request->all();
        foreach ($input['order'] as $key => $value) {
            $id       = $value['id'];
            $category = $this->categoryRepository->findWithoutFail($id);
            $this->categoryRepository->swapRows([
                'position' => $value['position']
            ], $category->id);
        }
        return $this->sendResponse([], 'Swaped Successfully');
    }


}
