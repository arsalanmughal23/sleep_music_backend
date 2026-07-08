<?php

use App\Models\Analytic;
use App\Repositories\Admin\AnalyticRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AnalyticRepositoryTest extends TestCase
{
    use MakeAnalyticTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AnalyticRepository
     */
    protected $analyticRepo;

    public function setUp()
    {
        parent::setUp();
        $this->analyticRepo = App::make(AnalyticRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAnalytic()
    {
        $analytic = $this->fakeAnalyticData();
        $createdAnalytic = $this->analyticRepo->create($analytic);
        $createdAnalytic = $createdAnalytic->toArray();
        $this->assertArrayHasKey('id', $createdAnalytic);
        $this->assertNotNull($createdAnalytic['id'], 'Created Analytic must have id specified');
        $this->assertNotNull(Analytic::find($createdAnalytic['id']), 'Analytic with given id must be in DB');
        $this->assertModelData($analytic, $createdAnalytic);
    }

    /**
     * @test read
     */
    public function testReadAnalytic()
    {
        $analytic = $this->makeAnalytic();
        $dbAnalytic = $this->analyticRepo->find($analytic->id);
        $dbAnalytic = $dbAnalytic->toArray();
        $this->assertModelData($analytic->toArray(), $dbAnalytic);
    }

    /**
     * @test update
     */
    public function testUpdateAnalytic()
    {
        $analytic = $this->makeAnalytic();
        $fakeAnalytic = $this->fakeAnalyticData();
        $updatedAnalytic = $this->analyticRepo->update($fakeAnalytic, $analytic->id);
        $this->assertModelData($fakeAnalytic, $updatedAnalytic->toArray());
        $dbAnalytic = $this->analyticRepo->find($analytic->id);
        $this->assertModelData($fakeAnalytic, $dbAnalytic->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAnalytic()
    {
        $analytic = $this->makeAnalytic();
        $resp = $this->analyticRepo->delete($analytic->id);
        $this->assertTrue($resp);
        $this->assertNull(Analytic::find($analytic->id), 'Analytic should not exist in DB');
    }
}
