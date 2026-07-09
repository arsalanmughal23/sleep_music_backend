<?php

namespace App\Http\Controllers\Admin;

use App\Criteria\CategoryCriteria;
use App\Criteria\PlaylistCriteria;
use App\Criteria\UserCriteria;
use App\Helper\BreadcrumbsRegister;
use App\DataTables\Admin\MediaDataTable;
use App\Helper\FileHelper;
use App\Helper\GenerateMixSoundHelper;
use App\Helper\NotificationsHelper;
use App\Helper\Util;
use App\Http\Requests\Admin\CreateMediaRequest;
use App\Http\Requests\Admin\ImportMediaStepOneRequest;
use App\Http\Requests\Admin\UpdateMediaRequest;
use App\Jobs\ImportMediaCsv;
use App\Models\Media;
use App\Models\Role;
use App\Models\User;
use App\Repositories\Admin\CategoryRepository;
use App\Repositories\Admin\MediaRepository;
use App\Http\Controllers\AppBaseController;
use App\Models\Category;
use App\Repositories\Admin\PlaylistRepository;
use App\Repositories\Admin\UserRepository;
use App\Traits\RequestCacheable;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class MediaController extends AppBaseController
{
    use RequestCacheable;
    
    protected $ffmepgPath;

    /** ModelName */
    private $ModelName;

    /** BreadCrumbName */
    private $BreadCrumbName;

    /** @var  MediaRepos
     * itory
     */
    private $mediaRepository;

    /** @var  CategoryRepository */
    private $categoryRepository;

    /** @var  UserRepository */
    private $userRepository;

    /** @var  PlaylistRepository */
    private $playlistRepository;

    public static $columns = [
        "category"       => "Category Column",
        "category_image" => "Category Image Column",
        "playlist"       => "Playlist Column",
        "playlist_image" => "Playlist Image Column",
        "artist"         => "Artist Column",
        "artist_image"   => "Artist Image Column",
        "name"           => "Name Column",
        "time"           => "Time Column",
        "location"       => "Location Column",
        "image"          => "Image Column",
    ];

    public $reqcSuffix = "media";

    public function __construct(
        MediaRepository $mediaRepo,
        CategoryRepository $categoryRepository,
        UserRepository $userRepository,
        PlaylistRepository $playlistRepository
    )
    {
        $this->mediaRepository    = $mediaRepo;
        $this->categoryRepository = $categoryRepository;
        $this->userRepository     = $userRepository;
        $this->playlistRepository = $playlistRepository;
        $this->ModelName          = 'medias';
        $this->BreadCrumbName     = 'Sounds';
        $this->ffmepgPath = config('constants.ffmpeg_bin_path');
    }

    /**
     * Display a listing of the Media.
     *
     * @param MediaDataTable $mediaDataTable
     * @return Response
     */
    public function index(MediaDataTable $mediaDataTable, Request $request)
    {
        $categories = Category::query()->where('is_mixer', 0)->get();
        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName);
        $mediaDataTable->category_id = $request->get('category_id', null);
        $mediaDataTable->is_premium = $request->get('premium_type', null);

        return $mediaDataTable->render('admin.medias.index', ['title' => $this->BreadCrumbName, 'categories' => $categories]);
    }

    /**
     * Show the form for creating a new Media.
     *
     * @return Response
     */
    public function create()
    {
        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName);

        $categories = $this->categoryRepository
            ->resetCriteria()
            ->pushCriteria(new CategoryCriteria([
                'is_mixer' => 0
            ]))
            ->all();

        $artists = $this->userRepository
            ->resetCriteria()
            ->pushCriteria(new UserCriteria([
                'role' => Role::ROLE_AUTHENTICATED
            ]))
            ->all()
            ->pluck('details.full_name', 'id')
            ->toArray();

        return view('admin.medias.create')->with([
            'title'      => $this->BreadCrumbName,
            'categories' => $categories,
            'artists'    => $artists
        ]);
    }

    /**
     * Store a newly created Media in storage.
     *
     * @param CreateMediaRequest $request
     *
     * @return Response
     */
    public function store(CreateMediaRequest $request)
    {
        $mediaDurationInSeconds = null;

        if ($request->hasFile('input_file')) {
            $file = $request->file('input_file');
            $FFMPEG = new GenerateMixSoundHelper();
            $optimizedFileAbsolutePath = $FFMPEG->optimizeServerTempFile($file);

            if (!$optimizedFileAbsolutePath) {
                Flash::error($this->BreadCrumbName . ' file optimization is failed!');
                return redirect(route('admin.medias.index'));
            }

            $optimizedFileStoragePath = storage_path('app/'.$optimizedFileAbsolutePath);
            $mediaDuration = $FFMPEG->getMediaDuration($optimizedFileStoragePath);
            $mediaDurationInSeconds = Util::timeToSeconds($mediaDuration);

            $fileS3Url = FileHelper::s3Upload($optimizedFileStoragePath);
            if (!$fileS3Url) {
                Flash::error($this->BreadCrumbName . ' file uploading on s3 is failed!');
                return redirect(route('admin.medias.index'));
            }

            $request['file_url'] = $fileS3Url;
            Storage::disk('local')->delete($optimizedFileAbsolutePath);
        }
        if ($request->hasFile('input_image')) {
            $image = $request->file('input_image');
            $image = $image->getPathName();
            $request['image'] = FileHelper::s3Upload($image);
        }
        $input = $request->only(['category_id', 'name', 'is_premium', 'image', 'file_url']);
        $input['duration'] = $mediaDurationInSeconds ?? config('constants.default_mixer_audio_length');

        $media = $this->mediaRepository->saveRecord($input);

        //TODO check premium send only subscribe users
        $message = "Discover our latest sleep sound! Check out the app now";
        $data    = [
            'notify_type' => 20,   // redirect audio listing screen
        ];

        $users = User::with('devices')->whereHas('details', function($builder){
            $builder->where('push_notifications', 1);
        })->get();

        if(count($users) > 0){
            foreach ($users as $user){
                $helperInstance = new NotificationsHelper();
                $helperInstance->sendPushNotifications($message, $user->devices, $data);
            }
        }

        $this->flushCache();

        Flash::success($this->BreadCrumbName . ' saved successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.medias.create'));
        } elseif (isset($request->translation)) {
            $redirect_to = redirect(route('admin.medias.edit', $media->id));
        } else {
            $redirect_to = redirect(route('admin.medias.index'));
        }
        return $redirect_to->with([
            'title' => $this->BreadCrumbName
        ]);
    }

    /**
     * Display the specified Media.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $media = $this->mediaRepository->findWithoutFail($id);

        if (empty($media)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.medias.index'));
        }

        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName, $media);
        return view('admin.medias.show')->with(['media' => $media, 'title' => $this->BreadCrumbName]);
    }

    /**
     * Show the form for editing the specified Media.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $media = $this->mediaRepository->findWithoutFail($id);

        if (empty($media)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.medias.index'));
        }


        $categories = $this->categoryRepository
            ->resetCriteria()
            ->pushCriteria(new CategoryCriteria([
                'is_mixer' => 0
            ]))
            ->all();

        $artists = $this->userRepository
            ->resetCriteria()
            ->pushCriteria(new UserCriteria([
                'role' => Role::ROLE_AUTHENTICATED
            ]))
            ->all()
            ->pluck('details.full_name', 'id')
            ->toArray();

        BreadcrumbsRegister::Register($this->ModelName, $this->BreadCrumbName, $media);
        return view('admin.medias.edit')->with([
            'media'      => $media,
            'title'      => $this->BreadCrumbName,
            'categories' => $categories,
            'artists'    => $artists
        ]);
    }

    /**
     * Update the specified Media in storage.
     *
     * @param  int $id
     * @param UpdateMediaRequest $request
     *
     * @return Response
     */
    public function update(UpdateMediaRequest $request, $id)
    {
        $media = $this->mediaRepository->findWithoutFail($id);
        $mediaDurationInSeconds = null;

        if (empty($media)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.medias.index'));
        }

        if ($request->hasFile('input_file')) {
            $file = $request->file('input_file');
            $FFMPEG = new GenerateMixSoundHelper();
            $optimizedFileAbsolutePath = $FFMPEG->optimizeServerTempFile($file);

            if (!$optimizedFileAbsolutePath) {
                Flash::error($this->BreadCrumbName . ' file optimization is failed!');
                return redirect(route('admin.medias.index'));
            }

            $optimizedFileStoragePath = storage_path('app/'.$optimizedFileAbsolutePath);
            $mediaDuration = $FFMPEG->getMediaDuration($optimizedFileStoragePath);
            $mediaDurationInSeconds = Util::timeToSeconds($mediaDuration);

            $fileS3Url = FileHelper::s3Upload($optimizedFileStoragePath);
            if (!$fileS3Url) {
                Flash::error($this->BreadCrumbName . ' file uploading on s3 is failed!');
                return redirect(route('admin.medias.index'));
            }

            $request['file_url'] = $fileS3Url;
            Storage::disk('local')->delete($optimizedFileAbsolutePath);
        }
        if ($request->hasFile('input_image')) {
            $image = $request->file('input_image');
            $image = $image->getPathName();
            $request['image'] = FileHelper::s3Upload($image);
        }
        $input = $request->only(['category_id', 'name', 'is_premium', 'image', 'file_url']);
        $input['duration'] = $mediaDurationInSeconds ?? $media->duration;

        $media = $this->mediaRepository->updateRecord($input, $id);
        $this->flushCache();

        Flash::success($this->BreadCrumbName . ' updated successfully.');
        if (isset($request->continue)) {
            $redirect_to = redirect(route('admin.medias.create'));
        } else {
            $redirect_to = redirect(route('admin.medias.index'));
        }
        return $redirect_to->with(['title' => $this->BreadCrumbName]);
    }

    /**
     * Remove the specified Media from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $media = $this->mediaRepository->findWithoutFail($id);

        if (empty($media)) {
            Flash::error($this->BreadCrumbName . ' not found');
            return redirect(route('admin.medias.index'));
        }

        $this->mediaRepository->deleteRecord($id);

        Flash::success($this->BreadCrumbName . ' deleted successfully.');
//        return redirect(route('admin.medias.index'))->with(['title' => $this->BreadCrumbName]);
        return back();
    }

    public function addToPlaylist(Media $media, Request $request)
    {
        if ($request->isMethod('post')) {
            $this->mediaRepository->syncPlaylist($media, $request);
            return response()->json(['success' => true]);
//            Flash::success($this->BreadCrumbName . ' updated successfully.');
//            return redirect(route('admin.medias.index'))->with(['title' => $this->BreadCrumbName]);
        }
        $playlists = $this->playlistRepository
            ->resetCriteria()
            ->pushCriteria(new PlaylistCriteria([
                'is_protected' => true,
//                'child_only'   => true,
                'has_child'    => 0,
            ]))
            ->all()->pluck('full_name', 'id')->toArray();

        return view('admin.ajax.add_to_playlist')->with(['playlists' => $playlists, 'media' => $media]);
    }

    public function importStep1()
    {
        BreadcrumbsRegister::custom("admin.import.media.1", "Import Media Step 1");
        return view('admin.medias.import.step1')->with(['title' => $this->BreadCrumbName]);
    }

    public function importStep2(Request $request)
    {
        BreadcrumbsRegister::custom("admin.import.media.2", "Import Media Step 2");

        //TODO: Get $csv_headers from Session.
//        $csv_headers = ["None", "Name", "Artist", "Album", "Category", "Time", "Location"];
        $csv_headers = $request->session()->get('csv_headers');

        return view('admin.medias.import.step2')->with(['title' => $this->BreadCrumbName, 'csv_headers' => $csv_headers, 'columns' => self::$columns]);
    }

    public function postStep1(ImportMediaStepOneRequest $request)
    {
        $has_headers       = $request->get('has_headers');
        $create_artists    = $request->get('create_artists');
        $create_categories = $request->get('create_categories');
        $create_playlists  = $request->get('create_playlists');
        $type              = $request->get('type');
        $file_path         = "";
        if ($request->hasFile('file')) {
            $file      = $request->file('file');
            $file_path = \Storage::path(\Storage::putFile('public/media_imports', $file));
        }
        if ($has_headers) {
            // If File has headers then ask the user to select the header columns;
            $headers = Util::getHeadersFromCsv($file_path);
            $request->session()->put('csv_headers', $headers);
            $request->session()->put('type', $type);
            $request->session()->put('has_headers', $has_headers);
            $request->session()->put('file_path', $file_path);
            $request->session()->put('create_artists', $create_artists);
            $request->session()->put('create_categories', $create_categories);
            $request->session()->put('create_playlists', $create_playlists);
            return redirect()->to(route('admin.import.media.2'));
        } else {
            // If file does not have headers then no need to ask the user to select header columns.
            // We will assume that the user has uploaded the file with the same sequence;
            $headers = array_keys(self::$columns);
            ImportMediaCsv::dispatch(\Auth::id(), $file_path, array_flip($headers), $type, $has_headers, $create_artists, $create_categories, $create_playlists);
        }

        Flash::success("Import Task has been dispatched.");
        return redirect(route('admin.medias.index'))->with(['title' => $this->BreadCrumbName]);
    }

    public function postStep2(Request $request)
    {
        $column_map = $request->only(array_keys(self::$columns));
        $request->session()->get('csv_headers');
        dispatch(
            new ImportMediaCsv(
                \Auth::id(),
                $request->session()->get('file_path'),
                $column_map,
                $request->session()->get('type'),
                $request->session()->get('has_headers'),
                $request->session()->get('create_artists'),
                $request->session()->get('create_categories'),
                $request->session()->get('create_playlists')
            )
        );

        Flash::success("Import Task has been dispatched.");
        return redirect(route('admin.medias..index'))->with(['title' => $this->BreadCrumbName]);
    }
}
