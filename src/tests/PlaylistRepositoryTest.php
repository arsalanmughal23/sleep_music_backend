<?php

use App\Models\Playlist;
use App\Repositories\Admin\PlaylistRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PlaylistRepositoryTest extends TestCase
{
    use MakePlaylistTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PlaylistRepository
     */
    protected $playlistRepo;

    public function setUp()
    {
        parent::setUp();
        $this->playlistRepo = App::make(PlaylistRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePlaylist()
    {
        $playlist = $this->fakePlaylistData();
        $createdPlaylist = $this->playlistRepo->create($playlist);
        $createdPlaylist = $createdPlaylist->toArray();
        $this->assertArrayHasKey('id', $createdPlaylist);
        $this->assertNotNull($createdPlaylist['id'], 'Created Playlist must have id specified');
        $this->assertNotNull(Playlist::find($createdPlaylist['id']), 'Playlist with given id must be in DB');
        $this->assertModelData($playlist, $createdPlaylist);
    }

    /**
     * @test read
     */
    public function testReadPlaylist()
    {
        $playlist = $this->makePlaylist();
        $dbPlaylist = $this->playlistRepo->find($playlist->id);
        $dbPlaylist = $dbPlaylist->toArray();
        $this->assertModelData($playlist->toArray(), $dbPlaylist);
    }

    /**
     * @test update
     */
    public function testUpdatePlaylist()
    {
        $playlist = $this->makePlaylist();
        $fakePlaylist = $this->fakePlaylistData();
        $updatedPlaylist = $this->playlistRepo->update($fakePlaylist, $playlist->id);
        $this->assertModelData($fakePlaylist, $updatedPlaylist->toArray());
        $dbPlaylist = $this->playlistRepo->find($playlist->id);
        $this->assertModelData($fakePlaylist, $dbPlaylist->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePlaylist()
    {
        $playlist = $this->makePlaylist();
        $resp = $this->playlistRepo->delete($playlist->id);
        $this->assertTrue($resp);
        $this->assertNull(Playlist::find($playlist->id), 'Playlist should not exist in DB');
    }
}
