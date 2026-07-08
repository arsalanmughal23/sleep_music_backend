<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AnalyticApiTest extends TestCase
{
    use MakeAnalyticTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAnalytic()
    {
        $analytic = $this->fakeAnalyticData();
        $this->json('POST', '/api/v1/analytics', $analytic);

        $this->assertApiResponse($analytic);
    }

    /**
     * @test
     */
    public function testReadAnalytic()
    {
        $analytic = $this->makeAnalytic();
        $this->json('GET', '/api/v1/analytics/'.$analytic->id);

        $this->assertApiResponse($analytic->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAnalytic()
    {
        $analytic = $this->makeAnalytic();
        $editedAnalytic = $this->fakeAnalyticData();

        $this->json('PUT', '/api/v1/analytics/'.$analytic->id, $editedAnalytic);

        $this->assertApiResponse($editedAnalytic);
    }

    /**
     * @test
     */
    public function testDeleteAnalytic()
    {
        $analytic = $this->makeAnalytic();
        $this->json('DELETE', '/api/v1/analytics/'.$analytic->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/analytics/'.$analytic->id);

        $this->assertResponseStatus(404);
    }
}
