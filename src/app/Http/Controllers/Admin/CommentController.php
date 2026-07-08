<?php

namespace App\Http\Controllers\Admin;

use App\Helper\BreadcrumbsRegister;
use App\DataTables\Admin\CommentDataTable;
use App\Http\Requests\Admin;
use App\Http\Requests\Admin\CreateCommentRequest;
use App\Http\Requests\Admin\UpdateCommentRequest;
use App\Repositories\Admin\CommentRepository;
use App\Http\Controllers\AppBaseController;
use Laracasts\Flash\Flash;
use Illuminate\Http\Response;

class CommentController extends AppBaseController
{
    /** ModelName */
    private $ModelName;

    /** BreadCrumbName */
    private $BreadCrumbName;

    /** @var  CommentRepository */
    private $commentRepository;

    public function __construct(CommentRepository $commentRepo)
    {
        $this->commentRepository = $commentRepo;
        $this->ModelName = 'comments';
        $this->BreadCrumbName = 'Comments';
    }

    /**
     * Display a listing of the Comment.
     *
     * @param CommentDataTable $commentDataTable
     * @return Response
     */
    public function index(CommentDataTable $commentDataTable)
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return $commentDataTable->render('admin.comments.index', ['title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for creating a new Comment.
     *
     * @return Response
     */
    public function create()
    {
        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName);
        return view('admin.comments.create')->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Store a newly created Comment in storage.
     *
     * @param CreateCommentRequest $request
     *
     * @return Response
     */
    public function store(CreateCommentRequest $request)
    {
        $comment = $this->commentRepository->saveRecord($request);

        Flash::success($this->BreadCrumbName . ' saved successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.comments.create'));
        } elseif (isset($request->translation)) {
            $redirect_to = redirect(route('admin.comments.edit', $comment->id));
        } else {
            $redirect_to = redirect(route('admin.comments.index'));
        }
        return $redirect_to->with([
            'title' => $this->BreadCrumbName
        ]);
    }

    /**
     * Display the specified Comment.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $comment = $this->commentRepository->findWithoutFail($id);

        if (empty($comment)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.comments.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $comment);
        return view('admin.comments.show')->with(['comment' => $comment, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for editing the specified Comment.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $comment = $this->commentRepository->findWithoutFail($id);

        if (empty($comment)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.comments.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName,$this->BreadCrumbName, $comment);
        return view('admin.comments.edit')->with(['comment' => $comment, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Update the specified Comment in storage.
     *
     * @param  int              $id
     * @param UpdateCommentRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCommentRequest $request)
    {
        $comment = $this->commentRepository->findWithoutFail($id);

        if (empty($comment)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.comments.index'));
        }

        $comment = $this->commentRepository->updateRecord($request, $comment);

        Flash::success($this->BreadCrumbName . ' updated successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.comments.create'));
        } else {
            $redirect_to = redirect(route('admin.comments.index'));
        }
        return $redirect_to->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Remove the specified Comment from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $comment = $this->commentRepository->findWithoutFail($id);

        if (empty($comment)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.comments.index'));
        }

        $this->commentRepository->deleteRecord($id);

        Flash::success($this->BreadCrumbName . ' deleted successfully.');
        return redirect(route('admin.comments.index'))->with(['title' => $this->BreadCrumbName]);
    }
}
