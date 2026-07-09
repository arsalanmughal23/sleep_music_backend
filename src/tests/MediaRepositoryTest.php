<?php

use App\Models\Media;
use App\Repositories\Admin\MediaRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MediaRepositoryTest extends TestCase
{
    use MakeMediaTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var MediaRepository
     */
    protected $mediaRepo;

    public function setUp()
    {
        parent::setUp();
        $this->mediaRepo = App::make(MediaRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateMedia()
    {
        $media = $this->fakeMediaData();
        $createdMedia = $this->mediaRepo->create($media);
        $createdMedia = $createdMedia->toArray();
        $this->assertArrayHasKey('id', $createdMedia);
        $this->assertNotNull($createdMedia['id'], 'Created Media must have id specified');
        $this->assertNotNull(Media::find($createdMedia['id']), 'Media with given id must be in DB');
        $this->assertModelData($media, $createdMedia);
    }

    /**
     * @test read
     */
    public function testReadMedia()
    {
        $media = $this->makeMedia();
        $dbMedia = $this->mediaRepo->find($media->id);
        $dbMedia = $dbMedia->toArray();
        $this->assertModelData($media->toArray(), $dbMedia);
    }

    /**
     * @test update
     */
    public function testUpdateMedia()
    {
        $media = $this->makeMedia();
        $fakeMedia = $this->fakeMediaData();
        $updatedMedia = $this->mediaRepo->update($fakeMedia, $media->id);
        $this->assertModelData($fakeMedia, $updatedMedia->toArray());
        $dbMedia = $this->mediaRepo->find($media->id);
        $this->assertModelData($fakeMedia, $dbMedia->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteMedia()
    {
        $media = $this->makeMedia();
        $resp = $this->mediaRepo->delete($media->id);
        $this->assertTrue($resp);
        $this->assertNull(Media::find($media->id), 'Media should not exist in DB');
    }
}
