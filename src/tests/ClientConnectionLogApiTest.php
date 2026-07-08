<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ClientConnectionLogApiTest extends TestCase
{
    use MakeClientConnectionLogTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateClientConnectionLog()
    {
        $clientConnectionLog = $this->fakeClientConnectionLogData();
        $this->json('POST', '/api/v1/clientConnectionLogs', $clientConnectionLog);

        $this->assertApiResponse($clientConnectionLog);
    }

    /**
     * @test
     */
    public function testReadClientConnectionLog()
    {
        $clientConnectionLog = $this->makeClientConnectionLog();
        $this->json('GET', '/api/v1/clientConnectionLogs/'.$clientConnectionLog->id);

        $this->assertApiResponse($clientConnectionLog->toArray());
    }

    /**
     * @test
     */
    public function testUpdateClientConnectionLog()
    {
        $clientConnectionLog = $this->makeClientConnectionLog();
        $editedClientConnectionLog = $this->fakeClientConnectionLogData();

        $this->json('PUT', '/api/v1/clientConnectionLogs/'.$clientConnectionLog->id, $editedClientConnectionLog);

        $this->assertApiResponse($editedClientConnectionLog);
    }

    /**
     * @test
     */
    public function testDeleteClientConnectionLog()
    {
        $clientConnectionLog = $this->makeClientConnectionLog();
        $this->json('DELETE', '/api/v1/clientConnectionLogs/'.$clientConnectionLog->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/clientConnectionLogs/'.$clientConnectionLog->id);

        $this->assertResponseStatus(404);
    }
}
