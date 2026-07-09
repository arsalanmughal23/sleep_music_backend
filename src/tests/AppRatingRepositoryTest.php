<?php

use App\Models\AppRating;
use App\Repositories\Admin\AppRatingRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AppRatingRepositoryTest extends TestCase
{
    use MakeAppRatingTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AppRatingRepository
     */
    protected $appRatingRepo;

    public function setUp()
    {
        parent::setUp();
        $this->appRatingRepo = App::make(AppRatingRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAppRating()
    {
        $appRating = $this->fakeAppRatingData();
        $createdAppRating = $this->appRatingRepo->create($appRating);
        $createdAppRating = $createdAppRating->toArray();
        $this->assertArrayHasKey('id', $createdAppRating);
        $this->assertNotNull($createdAppRating['id'], 'Created AppRating must have id specified');
        $this->assertNotNull(AppRating::find($createdAppRating['id']), 'AppRating with given id must be in DB');
        $this->assertModelData($appRating, $createdAppRating);
    }

    /**
     * @test read
     */
    public function testReadAppRating()
    {
        $appRating = $this->makeAppRating();
        $dbAppRating = $this->appRatingRepo->find($appRating->id);
        $dbAppRating = $dbAppRating->toArray();
        $this->assertModelData($appRating->toArray(), $dbAppRating);
    }

    /**
     * @test update
     */
    public function testUpdateAppRating()
    {
        $appRating = $this->makeAppRating();
        $fakeAppRating = $this->fakeAppRatingData();
        $updatedAppRating = $this->appRatingRepo->update($fakeAppRating, $appRating->id);
        $this->assertModelData($fakeAppRating, $updatedAppRating->toArray());
        $dbAppRating = $this->appRatingRepo->find($appRating->id);
        $this->assertModelData($fakeAppRating, $dbAppRating->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAppRating()
    {
        $appRating = $this->makeAppRating();
        $resp = $this->appRatingRepo->delete($appRating->id);
        $this->assertTrue($resp);
        $this->assertNull(AppRating::find($appRating->id), 'AppRating should not exist in DB');
    }
}
