<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PlaylistApiTest extends TestCase
{
    use MakePlaylistTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePlaylist()
    {
        $playlist = $this->fakePlaylistData();
        $this->json('POST', '/api/v1/playlists', $playlist);

        $this->assertApiResponse($playlist);
    }

    /**
     * @test
     */
    public function testReadPlaylist()
    {
        $playlist = $this->makePlaylist();
        $this->json('GET', '/api/v1/playlists/'.$playlist->id);

        $this->assertApiResponse($playlist->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePlaylist()
    {
        $playlist = $this->makePlaylist();
        $editedPlaylist = $this->fakePlaylistData();

        $this->json('PUT', '/api/v1/playlists/'.$playlist->id, $editedPlaylist);

        $this->assertApiResponse($editedPlaylist);
    }

    /**
     * @test
     */
    public function testDeletePlaylist()
    {
        $playlist = $this->makePlaylist();
        $this->json('DELETE', '/api/v1/playlists/'.$playlist->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/playlists/'.$playlist->id);

        $this->assertResponseStatus(404);
    }
}
