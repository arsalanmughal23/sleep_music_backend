<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ClientApiTest extends TestCase
{
    use MakeClientTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateClient()
    {
        $client = $this->fakeClientData();
        $this->json('POST', '/api/v1/clients', $client);

        $this->assertApiResponse($client);
    }

    /**
     * @test
     */
    public function testReadClient()
    {
        $client = $this->makeClient();
        $this->json('GET', '/api/v1/clients/'.$client->id);

        $this->assertApiResponse($client->toArray());
    }

    /**
     * @test
     */
    public function testUpdateClient()
    {
        $client = $this->makeClient();
        $editedClient = $this->fakeClientData();

        $this->json('PUT', '/api/v1/clients/'.$client->id, $editedClient);

        $this->assertApiResponse($editedClient);
    }

    /**
     * @test
     */
    public function testDeleteClient()
    {
        $client = $this->makeClient();
        $this->json('DELETE', '/api/v1/clients/'.$client->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/clients/'.$client->id);

        $this->assertResponseStatus(404);
    }
}
