<?php

use App\Models\Mediaview;
use App\Repositories\Admin\MediaviewRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MediaviewRepositoryTest extends TestCase
{
    use MakeMediaviewTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var MediaviewRepository
     */
    protected $mediaviewRepo;

    public function setUp()
    {
        parent::setUp();
        $this->mediaviewRepo = App::make(MediaviewRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateMediaview()
    {
        $mediaview = $this->fakeMediaviewData();
        $createdMediaview = $this->mediaviewRepo->create($mediaview);
        $createdMediaview = $createdMediaview->toArray();
        $this->assertArrayHasKey('id', $createdMediaview);
        $this->assertNotNull($createdMediaview['id'], 'Created Mediaview must have id specified');
        $this->assertNotNull(Mediaview::find($createdMediaview['id']), 'Mediaview with given id must be in DB');
        $this->assertModelData($mediaview, $createdMediaview);
    }

    /**
     * @test read
     */
    public function testReadMediaview()
    {
        $mediaview = $this->makeMediaview();
        $dbMediaview = $this->mediaviewRepo->find($mediaview->id);
        $dbMediaview = $dbMediaview->toArray();
        $this->assertModelData($mediaview->toArray(), $dbMediaview);
    }

    /**
     * @test update
     */
    public function testUpdateMediaview()
    {
        $mediaview = $this->makeMediaview();
        $fakeMediaview = $this->fakeMediaviewData();
        $updatedMediaview = $this->mediaviewRepo->update($fakeMediaview, $mediaview->id);
        $this->assertModelData($fakeMediaview, $updatedMediaview->toArray());
        $dbMediaview = $this->mediaviewRepo->find($mediaview->id);
        $this->assertModelData($fakeMediaview, $dbMediaview->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteMediaview()
    {
        $mediaview = $this->makeMediaview();
        $resp = $this->mediaviewRepo->delete($mediaview->id);
        $this->assertTrue($resp);
        $this->assertNull(Mediaview::find($mediaview->id), 'Mediaview should not exist in DB');
    }
}
