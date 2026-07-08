<?php

use App\Models\ClientConnectionLog;
use App\Repositories\Admin\ClientConnectionLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ClientConnectionLogRepositoryTest extends TestCase
{
    use MakeClientConnectionLogTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ClientConnectionLogRepository
     */
    protected $clientConnectionLogRepo;

    public function setUp()
    {
        parent::setUp();
        $this->clientConnectionLogRepo = App::make(ClientConnectionLogRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateClientConnectionLog()
    {
        $clientConnectionLog = $this->fakeClientConnectionLogData();
        $createdClientConnectionLog = $this->clientConnectionLogRepo->create($clientConnectionLog);
        $createdClientConnectionLog = $createdClientConnectionLog->toArray();
        $this->assertArrayHasKey('id', $createdClientConnectionLog);
        $this->assertNotNull($createdClientConnectionLog['id'], 'Created ClientConnectionLog must have id specified');
        $this->assertNotNull(ClientConnectionLog::find($createdClientConnectionLog['id']), 'ClientConnectionLog with given id must be in DB');
        $this->assertModelData($clientConnectionLog, $createdClientConnectionLog);
    }

    /**
     * @test read
     */
    public function testReadClientConnectionLog()
    {
        $clientConnectionLog = $this->makeClientConnectionLog();
        $dbClientConnectionLog = $this->clientConnectionLogRepo->find($clientConnectionLog->id);
        $dbClientConnectionLog = $dbClientConnectionLog->toArray();
        $this->assertModelData($clientConnectionLog->toArray(), $dbClientConnectionLog);
    }

    /**
     * @test update
     */
    public function testUpdateClientConnectionLog()
    {
        $clientConnectionLog = $this->makeClientConnectionLog();
        $fakeClientConnectionLog = $this->fakeClientConnectionLogData();
        $updatedClientConnectionLog = $this->clientConnectionLogRepo->update($fakeClientConnectionLog, $clientConnectionLog->id);
        $this->assertModelData($fakeClientConnectionLog, $updatedClientConnectionLog->toArray());
        $dbClientConnectionLog = $this->clientConnectionLogRepo->find($clientConnectionLog->id);
        $this->assertModelData($fakeClientConnectionLog, $dbClientConnectionLog->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteClientConnectionLog()
    {
        $clientConnectionLog = $this->makeClientConnectionLog();
        $resp = $this->clientConnectionLogRepo->delete($clientConnectionLog->id);
        $this->assertTrue($resp);
        $this->assertNull(ClientConnectionLog::find($clientConnectionLog->id), 'ClientConnectionLog should not exist in DB');
    }
}
