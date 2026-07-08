<?php

use App\Models\ReportType;
use App\Repositories\Admin\ReportTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTypeRepositoryTest extends TestCase
{
    use MakeReportTypeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ReportTypeRepository
     */
    protected $reportTypeRepo;

    public function setUp()
    {
        parent::setUp();
        $this->reportTypeRepo = App::make(ReportTypeRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateReportType()
    {
        $reportType = $this->fakeReportTypeData();
        $createdReportType = $this->reportTypeRepo->create($reportType);
        $createdReportType = $createdReportType->toArray();
        $this->assertArrayHasKey('id', $createdReportType);
        $this->assertNotNull($createdReportType['id'], 'Created ReportType must have id specified');
        $this->assertNotNull(ReportType::find($createdReportType['id']), 'ReportType with given id must be in DB');
        $this->assertModelData($reportType, $createdReportType);
    }

    /**
     * @test read
     */
    public function testReadReportType()
    {
        $reportType = $this->makeReportType();
        $dbReportType = $this->reportTypeRepo->find($reportType->id);
        $dbReportType = $dbReportType->toArray();
        $this->assertModelData($reportType->toArray(), $dbReportType);
    }

    /**
     * @test update
     */
    public function testUpdateReportType()
    {
        $reportType = $this->makeReportType();
        $fakeReportType = $this->fakeReportTypeData();
        $updatedReportType = $this->reportTypeRepo->update($fakeReportType, $reportType->id);
        $this->assertModelData($fakeReportType, $updatedReportType->toArray());
        $dbReportType = $this->reportTypeRepo->find($reportType->id);
        $this->assertModelData($fakeReportType, $dbReportType->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteReportType()
    {
        $reportType = $this->makeReportType();
        $resp = $this->reportTypeRepo->delete($reportType->id);
        $this->assertTrue($resp);
        $this->assertNull(ReportType::find($reportType->id), 'ReportType should not exist in DB');
    }
}
