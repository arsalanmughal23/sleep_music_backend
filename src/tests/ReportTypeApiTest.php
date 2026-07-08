<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReportTypeApiTest extends TestCase
{
    use MakeReportTypeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateReportType()
    {
        $reportType = $this->fakeReportTypeData();
        $this->json('POST', '/api/v1/reportTypes', $reportType);

        $this->assertApiResponse($reportType);
    }

    /**
     * @test
     */
    public function testReadReportType()
    {
        $reportType = $this->makeReportType();
        $this->json('GET', '/api/v1/reportTypes/'.$reportType->id);

        $this->assertApiResponse($reportType->toArray());
    }

    /**
     * @test
     */
    public function testUpdateReportType()
    {
        $reportType = $this->makeReportType();
        $editedReportType = $this->fakeReportTypeData();

        $this->json('PUT', '/api/v1/reportTypes/'.$reportType->id, $editedReportType);

        $this->assertApiResponse($editedReportType);
    }

    /**
     * @test
     */
    public function testDeleteReportType()
    {
        $reportType = $this->makeReportType();
        $this->json('DELETE', '/api/v1/reportTypes/'.$reportType->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/reportTypes/'.$reportType->id);

        $this->assertResponseStatus(404);
    }
}
