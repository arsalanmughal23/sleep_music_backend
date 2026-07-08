<?php

use App\Models\Client;
use App\Repositories\Admin\ClientRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ClientRepositoryTest extends TestCase
{
    use MakeClientTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ClientRepository
     */
    protected $clientRepo;

    public function setUp()
    {
        parent::setUp();
        $this->clientRepo = App::make(ClientRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateClient()
    {
        $client = $this->fakeClientData();
        $createdClient = $this->clientRepo->create($client);
        $createdClient = $createdClient->toArray();
        $this->assertArrayHasKey('id', $createdClient);
        $this->assertNotNull($createdClient['id'], 'Created Client must have id specified');
        $this->assertNotNull(Client::find($createdClient['id']), 'Client with given id must be in DB');
        $this->assertModelData($client, $createdClient);
    }

    /**
     * @test read
     */
    public function testReadClient()
    {
        $client = $this->makeClient();
        $dbClient = $this->clientRepo->find($client->id);
        $dbClient = $dbClient->toArray();
        $this->assertModelData($client->toArray(), $dbClient);
    }

    /**
     * @test update
     */
    public function testUpdateClient()
    {
        $client = $this->makeClient();
        $fakeClient = $this->fakeClientData();
        $updatedClient = $this->clientRepo->update($fakeClient, $client->id);
        $this->assertModelData($fakeClient, $updatedClient->toArray());
        $dbClient = $this->clientRepo->find($client->id);
        $this->assertModelData($fakeClient, $dbClient->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteClient()
    {
        $client = $this->makeClient();
        $resp = $this->clientRepo->delete($client->id);
        $this->assertTrue($resp);
        $this->assertNull(Client::find($client->id), 'Client should not exist in DB');
    }
}
