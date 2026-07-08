<?php

use App\Models\Follow;
use App\Repositories\Admin\FollowRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FollowRepositoryTest extends TestCase
{
    use MakeFollowTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var FollowRepository
     */
    protected $followRepo;

    public function setUp()
    {
        parent::setUp();
        $this->followRepo = App::make(FollowRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateFollow()
    {
        $follow = $this->fakeFollowData();
        $createdFollow = $this->followRepo->create($follow);
        $createdFollow = $createdFollow->toArray();
        $this->assertArrayHasKey('id', $createdFollow);
        $this->assertNotNull($createdFollow['id'], 'Created Follow must have id specified');
        $this->assertNotNull(Follow::find($createdFollow['id']), 'Follow with given id must be in DB');
        $this->assertModelData($follow, $createdFollow);
    }

    /**
     * @test read
     */
    public function testReadFollow()
    {
        $follow = $this->makeFollow();
        $dbFollow = $this->followRepo->find($follow->id);
        $dbFollow = $dbFollow->toArray();
        $this->assertModelData($follow->toArray(), $dbFollow);
    }

    /**
     * @test update
     */
    public function testUpdateFollow()
    {
        $follow = $this->makeFollow();
        $fakeFollow = $this->fakeFollowData();
        $updatedFollow = $this->followRepo->update($fakeFollow, $follow->id);
        $this->assertModelData($fakeFollow, $updatedFollow->toArray());
        $dbFollow = $this->followRepo->find($follow->id);
        $this->assertModelData($fakeFollow, $dbFollow->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteFollow()
    {
        $follow = $this->makeFollow();
        $resp = $this->followRepo->delete($follow->id);
        $this->assertTrue($resp);
        $this->assertNull(Follow::find($follow->id), 'Follow should not exist in DB');
    }
}
