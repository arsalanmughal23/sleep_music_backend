<?php

namespace App\Jobs;

use App\Helper\Util;
use App\Models\Category;
use App\Models\Role;
use App\Notifications\SendImportResult;
use App\Repositories\Admin\CategoryRepository;
use App\Repositories\Admin\MediaRepository;
use App\Repositories\Admin\PlaylistRepository;
use App\Repositories\Admin\UserDetailRepository;
use App\Repositories\Admin\UserRepository;
use App\Traits\RequestCacheable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportMediaCsv implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RequestCacheable;

    /**
     * User who started the import. This user will get the email notification of the task.
     * @var $initiator null
     */
    private $initiator = null;

    private $file_path;
    private $column_map        = [];
    private $type              = Category::TYPE_AUDIO;
    private $has_headers       = false;
    private $create_artists    = false;
    private $create_playlists  = false;
    private $create_categories = false;
    private $ignored_rows      = [];

    /** @var UserRepository */
    private $userRepository;
    private $userDetailsRepository;
    private $mediaRepository;
    private $categoryRepository;
    private $playlistRepository;


    /**
     * Create a new job instance.
     *
     * @param $initiator
     * @param $file_path
     * @param $column_map
     * @param $type
     * @param $has_headers
     * @param $create_artists
     * @param $create_categories
     * @param $create_playlists
     */
    public function __construct($initiator, $file_path, $column_map, $type, $has_headers, $create_artists, $create_categories, $create_playlists)
    {
        $this->initiator         = $initiator;
        $this->file_path         = $file_path;
        $this->column_map        = $column_map;
        $this->type              = $type;
        $this->has_headers       = $has_headers;
        $this->create_artists    = $create_artists;
        $this->create_categories = $create_categories;
        $this->create_playlists  = $create_playlists;

    }

    private function initRepositories()
    {
        // Repositories
        $this->userRepository        = app(UserRepository::class);
        $this->userDetailsRepository = app(UserDetailRepository::class);
        $this->mediaRepository       = app(MediaRepository::class);
        $this->categoryRepository    = app(CategoryRepository::class);
        $this->playlistRepository    = app(PlaylistRepository::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->initRepositories();
        $total_imported = 0;

        foreach (Util::getDataFromCsv($this->file_path, $this->has_headers) as $row) {
            $values = [];
            $data   = [];
            foreach ($this->column_map as $column => $index) {
                $values[$column] = $row[$index];
            }
            $reason = "";
            switch (true) {
                case empty($values['location']):
                    $reason = "MP3 Location was empty";
                    break;
                case empty($values['artist']):
                    $reason = "Artist was empty";
                    break;
                case empty($values['playlist']):
                    $reason = "Playlist was empty";
                    break;
                case empty($values['category']):
                    $reason = "Category was empty";
                    break;
            }

            if (!empty($reason)) {
                $values['reason'] = $reason;
                array_push($this->ignored_rows, $values);
                continue;
            }

            $data['name']         = $values['name'];
            $data['media_length'] = $values['time'];
            $data['image']        = $values['image'];
            $data['file_path']    = $values['location'];
            $data['user_id']      = $values['artist'];
            $data['file_type']    = $this->type;
            $data['is_featured']  = 0;
            $playlist             = $values['playlist'];
            $data['category_id']  = $values['category'];
            if ($this->create_artists) {
                $data['user_id'] = $this->findOrCreateArtist($values['artist'], $values['artist_image']);
            }
            if ($this->create_categories) {
                $category_id = $this->findOrCreateCategory($values['category'], $values['category_image'], $this->type);
                if (!$category_id) {
                    $values['reason'] = "Category Image not found";
                    array_push($this->ignored_rows, $values);
                    continue;
                }
                $data['category_id'] = $category_id;
            }
            if ($this->create_playlists) {
                $playlist = $this->findOrCreatePlaylist($values['playlist'], $values['playlist_image'], $this->type, $data['category_id']);
                if (count($playlist) <= 0) {
                    $values['reason'] = "Default Playlist not found nor can be created because Playlist Image not found";
                    array_push($this->ignored_rows, $values);
                    continue;
                }

            }
            // Find media with Name, Category, Length, Artist ID and File Type in our database;
            // If any of these fields are not matched, we can create another media on the same name.
            $media = $this->mediaRepository->findWhere([
                'name'         => $data['name'],
                'category_id'  => $data['category_id'],
                'media_length' => $data['media_length'],
                'user_id'      => $data['user_id'],
                'file_type'    => $data['file_type'],
            ])->first();
            if (!$media) {
                $mediaObject = $this->createMediaObject($data);
                if (!$mediaObject) {
                    $values['reason'] = "MP3 file did not found on storage.";
                    array_push($this->ignored_rows, $values);
                    continue;
                }
                $media = $this->mediaRepository->create($mediaObject);
            }

            $this->mediaRepository->syncPlaylist($media, $playlist);
            $total_imported++;
        }

        // Notify the user that import job has been completed;
        $user = $this->userRepository->findWithoutFail($this->initiator);
        if (!empty($user)) {
            $user->notify(new SendImportResult($this->ignored_rows, $total_imported));
        }
        $this->flushCache();
    }

    public function findOrCreateArtist($name, $image)
    {
        // TODO: Find Artist or Create One;
        $email = str_slug($name) . "@artist.noda.com";
        // Cannot use updateOrCreate because if we use it, we have to handle the image before checking if its needed or not. which increases the process time.
        // 'name' => $name,
        $user = $this->userRepository->findWhere(['email' => $email])->first();
        if (!$user) {
            $user = $this->userRepository->create(['name' => $name, 'email' => $email, 'password' => bcrypt(str_random())]);
        }
        $details = $this->userDetailsRepository->findWhere(['user_id' => $user->id])->first();
        if (!$details) {
            $filePath = $this->handleImageFile($image, "users");
            $arr      = ['user_id' => $user->id, 'first_name' => $name];
            if (!!$filePath) {
                $arr['image'] = $filePath;
            }
            $this->userDetailsRepository->create($arr);
        }
        /*
        $user = $this->userRepository->updateOrCreate(
            ['name' => $name, 'email' => $email],
            ['name' => $name, 'email' => $email, 'password' => bcrypt(str_random())]
        );

        $this->userDetailsRepository->updateOrCreate(
            ['user_id' => $user->id],
            ['user_id' => $user->id, 'first_name' => $name, 'image' => $this->handleImageFile($image, "users")]
        );
        */

        $user->roles()->sync([Role::ROLE_ARTIST]);

        // 'image' => $image
        return $user->id;
    }

    public function findOrCreateCategory($name, $image, $type)
    {
        $category = $this->categoryRepository->findWhere(['name' => $name, 'type' => $type])->first();
        if (!$category) {
            $image_path = $this->handleImageFile($image, "public/category_images");
            if (!$image_path) {
                return false;
            }
            $category = $this->categoryRepository->create(
                ['name' => $name, 'image' => $image_path, 'type' => $type]
            );
        }
        return $category->id;
    }

    public function findOrCreatePlaylist($name, $image, $type, $category_id = null)
    {
        $ret       = [];
        $playlists = explode(",", $name);
        $images    = explode(",", $image);
        foreach ($playlists as $key => $name) {
            $image    = isset($images[$key]) ? $images[$key] : $images[0];
            $playlist = $this->playlistRepository->findWhere(['name' => $name, 'type' => $type])->first();
            if (!$playlist) {
                $image_path = $this->handleImageFile($image, "public/playlist_images");
                if (!$image_path) {
                    continue;
                }
                $playlist = $this->playlistRepository->create(
                    [
                        'name'         => $name,
                        'image'        => $image_path,
                        'type'         => $type,
                        'is_protected' => 1,
                        'user_id'      => null,
                        'category_id'  => $category_id
                    ]
                );
            }
            $ret[] = $playlist->id;
        }

        return $ret;
    }


    public function createMediaObject($create_media)
    {
        if (!empty($create_media['file_path'])) {
            try {
                $extension = pathinfo($create_media['file_path'])['extension'];
                $new_path  = 'public/media_files/' . str_random(20) . "." . $extension;
                if (\Storage::copy($create_media['file_path'], $new_path)) {
                    $create_media['file_path'] = $new_path;
                }
            } catch (\Exception $e) {
                return false;
            }
        }
        $image_path = $this->handleImageFile($create_media['image'], 'public/media_images');
        if ($image_path) {
            $create_media['image'] = $image_path;
        }
        return $create_media;
    }

    public function handleImageFile($old_path, $dir = "public")
    {
        if (empty($old_path)) {
            return $old_path;
        }
        try {
            // We are using copy because one image file can be referred to multiple media records.
            // Storage::copy is used because storage is AWS S3.
            $extension = pathinfo($old_path)['extension'];
            $new_path  = $dir . "/" . str_random(20) . "." . $extension;
            if (\Storage::copy($old_path, $new_path)) {
                return $new_path;
            }
        } catch (\Exception $e) {
            return false;
        }
        return $old_path;

    }
}
