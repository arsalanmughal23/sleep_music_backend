<?php

use App\Models\DeleteType;
use App\Repositories\Admin\DeleteTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DeleteTypeRepositoryTest extends TestCase
{
    use MakeDeleteTypeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DeleteTypeRepository
     */
    protected $deleteTypeRepo;

    public function setUp()
    {
        parent::setUp();
        $this->deleteTypeRepo = App::make(DeleteTypeRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDeleteType()
    {
        $deleteType = $this->fakeDeleteTypeData();
        $createdDeleteType = $this->deleteTypeRepo->create($deleteType);
        $createdDeleteType = $createdDeleteType->toArray();
        $this->assertArrayHasKey('id', $createdDeleteType);
        $this->assertNotNull($createdDeleteType['id'], 'Created DeleteType must have id specified');
        $this->assertNotNull(DeleteType::find($createdDeleteType['id']), 'DeleteType with given id must be in DB');
        $this->assertModelData($deleteType, $createdDeleteType);
    }

    /**
     * @test read
     */
    public function testReadDeleteType()
    {
        $deleteType = $this->makeDeleteType();
        $dbDeleteType = $this->deleteTypeRepo->find($deleteType->id);
        $dbDeleteType = $dbDeleteType->toArray();
        $this->assertModelData($deleteType->toArray(), $dbDeleteType);
    }

    /**
     * @test update
     */
    public function testUpdateDeleteType()
    {
        $deleteType = $this->makeDeleteType();
        $fakeDeleteType = $this->fakeDeleteTypeData();
        $updatedDeleteType = $this->deleteTypeRepo->update($fakeDeleteType, $deleteType->id);
        $this->assertModelData($fakeDeleteType, $updatedDeleteType->toArray());
        $dbDeleteType = $this->deleteTypeRepo->find($deleteType->id);
        $this->assertModelData($fakeDeleteType, $dbDeleteType->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDeleteType()
    {
        $deleteType = $this->makeDeleteType();
        $resp = $this->deleteTypeRepo->delete($deleteType->id);
        $this->assertTrue($resp);
        $this->assertNull(DeleteType::find($deleteType->id), 'DeleteType should not exist in DB');
    }
}
