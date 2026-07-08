<?php

use Faker\Factory as Faker;
use App\Models\Playlist;
use App\Repositories\Admin\PlaylistRepository;

trait MakePlaylistTrait
{
    /**
     * Create fake instance of Playlist and save it in database
     *
     * @param array $playlistFields
     * @return Playlist
     */
    public function makePlaylist($playlistFields = [])
    {
        /** @var PlaylistRepository $playlistRepo */
        $playlistRepo = App::make(PlaylistRepository::class);
        $theme = $this->fakePlaylistData($playlistFields);
        return $playlistRepo->create($theme);
    }

    /**
     * Get fake instance of Playlist
     *
     * @param array $playlistFields
     * @return Playlist
     */
    public function fakePlaylist($playlistFields = [])
    {
        return new Playlist($this->fakePlaylistData($playlistFields));
    }

    /**
     * Get fake data of Playlist
     *
     * @param array $postFields
     * @return array
     */
    public function fakePlaylistData($playlistFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'user_id' => $fake->word,
            'name' => $fake->word,
            'image' => $fake->word,
            'type' => $fake->word,
            'is_featured' => $fake->word,
            'is_protected' => $fake->word,
            'sort_key' => $fake->word,
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s'),
            'deleted_at' => $fake->date('Y-m-d H:i:s')
        ], $playlistFields);
    }
}
