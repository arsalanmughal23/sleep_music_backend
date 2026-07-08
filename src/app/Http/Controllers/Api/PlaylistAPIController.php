<?php

namespace App\Http\Controllers\Api;

use App\Criteria\PlaylistCriteria;
use App\Http\Requests\Api\MediaToPlaylistApiRequest;
use App\Http\Requests\Api\CreatePlaylistAPIRequest;
use App\Http\Requests\Api\UpdatePlaylistAPIRequest;
use App\Models\Playlist;
use App\Repositories\Admin\PlaylistRepository;
use App\Traits\RequestCacheable;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Http\Response;

/**
 * Class PlaylistController
 * @package App\Http\Controllers\Api
 */
class PlaylistAPIController extends AppBaseController
{
    use RequestCacheable;

    /** @var  PlaylistRepository */
    private $playlistRepository;

    public $reqcSuffix = "playlist";

    public function __construct(PlaylistRepository $playlistRepo)
    {
        $this->playlistRepository = $playlistRepo;
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @return Response
     *
     * @SWG\Get(
     *      path="/playlists",
     *      summary="Get a listing of the Playlists.",
     *      tags={"Playlist"},
     *      description="Get all Playlists",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="Authorization",
     *          description="User Auth Token{ Bearer ABC123 }",
     *          type="string",
     *          required=true,
     *          default="Bearer ABC123",
     *          in="header"
     *      ),
     *      @SWG\Parameter(
     *          name="type",
     *          description="Acceptable values are 10 and 20, 10 will return only Audio (Music) Playlists, 20 will return only Video Playlists. If not found, Returns All Records in DB.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="parent_id",
     *          description="Filter playlist by parent_id. If not found, Returns All Records in DB.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="category_id",
     *          description="Filter playlist by category_id. If not found, Returns All Records in DB.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="parent_only",
     *          description="Get parent playlists only. If not found, Returns All Records in DB.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="child_only",
     *          description="Get child playlists only. If not found, Returns All Records in DB.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="has_child",
     *          description="Filter playlist by child status, 1 will only include playlists that has children, 0 will only include playlist that does not have children. If not found, Returns All Records in DB.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="is_featured",
     *          description="Acceptable values are 0 and 1, 0 will return exclude featured playlists, 1 will return only featured playlists,. If not found, Returns All Records in DB.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="is_protected",
     *          description="Acceptable values are 0 and 1, 0 will return only playlists that are not protected (User Created include Other Users), 1 will return only playlists that are protected (Only Admin Created) . If not found, Returns All Records in DB.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="exclude_empty",
     *          description="Acceptable values are 0 and 1, 1 will exclude all playlists that does not have any songs selected in them, 0 will return all playlists.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="sort_by_songs",
     *          description="Sort all playlists be song count desc.",
     *          type="integer",
     *          default=1,
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="is_mine",
     *          description="Return only playlists that are mine (Current User Created). If not found, Returns All Records in DB.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="with_media",
     *          description="Return all playlists along with media records. If not found only playlists records are returned without media. This improves performance when dealing with playlists that have many media records included",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="updated_after",
     *          description="Return all playlists that are updated after the given time. If not found only playlists records are returned. This improves performance when getting updates frequently. Please use the updated_at as recieved from previous APIs or yyyy-mm-dd hh:mm:ss for correct date comparison",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="limit",
     *          description="Change the Default Record Count. If not found, Returns All Records in DB.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *     @SWG\Parameter(
     *          name="offset",
     *          description="Change the Default Offset of the Query. If not found, 0 will be used.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/Playlist")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $params = ['is_featured', 'is_protected', 'is_mine', 'with_media', 'updated_after', 'exclude_empty', 'sort_by_songs', 'type', 'category_id', 'parent_only', 'child_only', 'parent_id', 'has_child', 'query'];
        if ($request->get('is_mine', false)) {
            $this->avoidCache = true;
        }
        return $this->cacheRequest($request->only($this->mergeDefaultParamsWithControllerParams($params)), function () use ($request, $params) {
            $playlists = $this->playlistRepository
                ->pushCriteria(new RequestCriteria($request))
                ->pushCriteria(new LimitOffsetCriteria($request))
                ->pushCriteria(new PlaylistCriteria(
                    $request->only($params)
                ))
                ->paginate(env("RECORDS_LIMIT", 20));

            $result['total_pages'] = $playlists->lastPage();
            $data                  = $playlists->items();
            $result['data']        = $data;

//            if ($request->get('with_media', 0) == 1) {
//                $playlists->map(function ($item) {
//                    return $item->makeVisible(['media']);
//                });
//            }

            return $this->sendResponse($result, 'Playlists retrieved successfully');
        });

    }

    /**
     * @param CreatePlaylistAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/playlists",
     *      summary="Store a newly created Playlist in storage",
     *      tags={"Playlist"},
     *      description="Store Playlist",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="Authorization",
     *          description="User Auth Token{ Bearer ABC123 }",
     *          type="string",
     *          required=true,
     *          default="Bearer ABC123",
     *          in="header"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Playlist that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Playlist")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Playlist"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePlaylistAPIRequest $request)
    {


        $playlists = $this->playlistRepository->saveRecord($request, true);

        return $this->sendResponse($playlists->toArray(), 'Playlist saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/playlists/{id}",
     *      summary="Display the specified Playlist",
     *      tags={"Playlist"},
     *      description="Get Playlist",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="Authorization",
     *          description="User Auth Token{ Bearer ABC123 }",
     *          type="string",
     *          required=true,
     *          default="Bearer ABC123",
     *          in="header"
     *      ),
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Playlist",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="with_media",
     *          description="Return all playlists along with media records. If not found only playlists records are returned without media. This improves performance when dealing with playlists that have many media records included",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Playlist"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id, Request $request)
    {
        /** @var Playlist $playlist */
        $playlist = $this->playlistRepository
            ->pushCriteria(new LimitOffsetCriteria($request))
            ->pushCriteria(new PlaylistCriteria(
                $request->only(['with_media'])
            ))
            ->findWithoutFail($id)
            ->paginate(env("RECORDS_LIMIT", 20));

        $result['total_pages'] = $playlist->lastPage();
        $data                  = $playlist->items();
        $result['data']        = $data;

//            if ($request->get('with_media', 0) == 1) {
//                $playlists->map(function ($item) {
//                    return $item->makeVisible(['media']);
//                });
//            }

        return $this->sendResponse($result, 'Playlists retrieved successfully');

        if (empty($playlist)) {
            return $this->sendError('Playlist not found');
        }

        return $this->sendResponse($playlist, 'Playlist retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePlaylistAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/playlists/{id}",
     *      summary="Update the specified Playlist in storage",
     *      tags={"Playlist"},
     *      description="Update Playlist",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="Authorization",
     *          description="User Auth Token{ Bearer ABC123 }",
     *          type="string",
     *          required=true,
     *          default="Bearer ABC123",
     *          in="header"
     *      ),
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Playlist",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Playlist that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Playlist")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Playlist"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePlaylistAPIRequest $request)
    {
//        dd($request);
        $playlist = $this->playlistRepository->findWithoutFail($id);

        if (empty($playlist)) {
            return $this->sendError('Playlist not found');
        }

        $playlist = $this->playlistRepository->updateRecord($request, $playlist, true);

        return $this->sendResponse($playlist->toArray(), 'Playlist updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/playlists/{id}",
     *      summary="Remove the specified Playlist from storage",
     *      tags={"Playlist"},
     *      description="Delete Playlist",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="Authorization",
     *          description="User Auth Token{ Bearer ABC123 }",
     *          type="string",
     *          required=true,
     *          default="Bearer ABC123",
     *          in="header"
     *      ),
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Playlist",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {

        /** @var Playlist $playlist */
        $playlist = $this->playlistRepository->findWithoutFail($id);

        if (empty($playlist)) {

            return $this->sendErrorWithData(['Playlist not found']);
        }

        if ($playlist->user_id != \Auth::id()) {
            return $this->sendErrorWithData(['You are not authorized to delete this resource']);
        }

        $this->playlistRepository->deleteRecord($id);

        return $this->sendResponse([], 'Playlist deleted successfully');
    }

    /**
     * @param Playlist $playlist
     * @param MediaToPlaylistApiRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/playlists/sync-media/{id}",
     *      summary="Sync media to playlist",
     *      tags={"Playlist"},
     *      description="Sync Media to Playlist - remove media ids which are not present in media param and add those which are not found in database.",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="Authorization",
     *          description="User Auth Token{ Bearer ABC123 }",
     *          type="string",
     *          required=true,
     *          default="Bearer ABC123",
     *          in="header"
     *      ),
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Playlist",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="media",
     *          description="id or csv of ids of media",
     *          type="integer",
     *          required=true,
     *          in="formData"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Playlist"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function syncMediaToPlaylist(Playlist $playlist, MediaToPlaylistApiRequest $request)
    {
        if (empty($playlist)) {
            return $this->sendErrorWithData(['Playlist not found']);
        }

        $this->playlistRepository->syncMedia($playlist, $request);

        /** @var Playlist $playlist */
        $playlist = $this->getPlaylistWithMedia($playlist->id);

        return $this->sendResponse($playlist->toArray(), 'Playlist updated successfully');

    }

    /**
     * @param Playlist $playlist
     * @param MediaToPlaylistApiRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/playlists/add-media/{id}",
     *      summary="Add media to playlist",
     *      tags={"Playlist"},
     *      description="Add Media to Playlist",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="Authorization",
     *          description="User Auth Token{ Bearer ABC123 }",
     *          type="string",
     *          required=true,
     *          default="Bearer ABC123",
     *          in="header"
     *      ),
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Playlist",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="media",
     *          description="id or csv of ids of media",
     *          type="integer",
     *          required=true,
     *          in="formData"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Playlist"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function addMediaToPlaylist(Playlist $playlist, MediaToPlaylistApiRequest $request)
    {
        //dd($request->media);
        if (empty($playlist)) {
            return $this->sendErrorWithData(['Playlist not found']);
        }

        $this->playlistRepository->attachMedia($playlist, $request);


        //dd("here");
        /** @var Playlist $playlist */
        $playlist = $this->getPlaylistWithMedia($playlist->id);

        return $this->sendResponse($playlist->toArray(), 'Playlist updated successfully');

    }

    /**
     * @param Playlist $playlist
     * @param MediaToPlaylistApiRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/playlists/remove-media/{id}",
     *      summary="Remove media from playlist",
     *      tags={"Playlist"},
     *      description="Remove Media to Playlist",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="Authorization",
     *          description="User Auth Token{ Bearer ABC123 }",
     *          type="string",
     *          required=true,
     *          default="Bearer ABC123",
     *          in="header"
     *      ),
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Playlist",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="media",
     *          description="id or csv of ids of media",
     *          type="integer",
     *          required=true,
     *          in="formData"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Playlist"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function removeMediaToPlaylist(Playlist $playlist, MediaToPlaylistApiRequest $request)
    {

        if (empty($playlist)) {
            return $this->sendErrorWithData(['Playlist not found']);
        }

        $this->playlistRepository->detachMedia($playlist, $request);

        /** @var Playlist $playlist */
        $playlist = $this->getPlaylistWithMedia($playlist->id);

        return $this->sendResponse($playlist->toArray(), 'Playlist updated successfully');

    }


    private function getPlaylistWithMedia($playlist_id)
    {
        $playlist = $this->playlistRepository->pushCriteria(
            new PlaylistCriteria(["with_media" => true])
        )->findWithoutFail($playlist_id);

        $playlist->makeVisible('media');
        return $playlist;
    }


    public function search(Request $request)
    {
        $query      = $request->get('query');
        $role       = Role::ROLE_ARTIST;
        $users      = $this->userRepository
            ->resetCriteria()
            ->pushCriteria(new RequestCriteria($request))
            ->pushCriteria(new LimitOffsetCriteria($request))
            ->pushCriteria(new UserCriteria([
                'role'  => $role,
                'query' => $query
            ]))
            ->all();
        $media      = $this->mediaRepository
            ->resetCriteria()
            ->pushCriteria(new RequestCriteria($request))
            ->pushCriteria(new LimitOffsetCriteria($request))
            ->pushCriteria(new MediaCriteria([
                'query' => $query
            ]))
            ->all();
        $playlists  = $this->playlistRepository
            ->resetCriteria()
            ->pushCriteria(new RequestCriteria($request))
            ->pushCriteria(new LimitOffsetCriteria($request))
            ->pushCriteria(new PlaylistCriteria([
                'is_protected' => true,
                'query'        => $query
            ]))
            ->all();
        $categories = $this->categoryRepository
            ->resetCriteria()
            ->pushCriteria(new RequestCriteria($request))
            ->pushCriteria(new LimitOffsetCriteria($request))
            ->pushCriteria(new CategoryCriteria([
                'query' => $query
            ]))
            ->all();

        return $this->sendResponse([
            'users'      => $users->toArray(),
            'media'      => $media->toArray(),
            'playlists'  => $playlists->toArray(),
            'categories' => $categories->toArray(),
        ], 'Data retrieved successfully');
    }


    public function playlistMe()
    {
        $id       = Auth::id();
        $playlist = $this->playlistRepository
            ->findWhere(['user_id' => $id])->first()
            ->paginate(env("RECORDS_LIMIT", 20));

        $result['total_pages'] = $playlist->lastPage();
        $data                  = $playlist->items();
        $result['data']        = $data;


        if (empty($result)) {
            return $this->sendErrorWithData(['Playlist not found']);
        }
        return $this->sendResponse($result, 'Playlists retrieved successfully');


        return $this->sendResponse($playlist, 'Playlist retrieved successfully');
    }


}
