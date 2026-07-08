<?php

use App\Models\TrendingArtist;
use App\Repositories\Admin\TrendingArtistRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TrendingArtistRepositoryTest extends TestCase
{
    use MakeTrendingArtistTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var TrendingArtistRepository
     */
    protected $trendingArtistRepo;

    public function setUp()
    {
        parent::setUp();
        $this->trendingArtistRepo = App::make(TrendingArtistRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateTrendingArtist()
    {
        $trendingArtist = $this->fakeTrendingArtistData();
        $createdTrendingArtist = $this->trendingArtistRepo->create($trendingArtist);
        $createdTrendingArtist = $createdTrendingArtist->toArray();
        $this->assertArrayHasKey('id', $createdTrendingArtist);
        $this->assertNotNull($createdTrendingArtist['id'], 'Created TrendingArtist must have id specified');
        $this->assertNotNull(TrendingArtist::find($createdTrendingArtist['id']), 'TrendingArtist with given id must be in DB');
        $this->assertModelData($trendingArtist, $createdTrendingArtist);
    }

    /**
     * @test read
     */
    public function testReadTrendingArtist()
    {
        $trendingArtist = $this->makeTrendingArtist();
        $dbTrendingArtist = $this->trendingArtistRepo->find($trendingArtist->id);
        $dbTrendingArtist = $dbTrendingArtist->toArray();
        $this->assertModelData($trendingArtist->toArray(), $dbTrendingArtist);
    }

    /**
     * @test update
     */
    public function testUpdateTrendingArtist()
    {
        $trendingArtist = $this->makeTrendingArtist();
        $fakeTrendingArtist = $this->fakeTrendingArtistData();
        $updatedTrendingArtist = $this->trendingArtistRepo->update($fakeTrendingArtist, $trendingArtist->id);
        $this->assertModelData($fakeTrendingArtist, $updatedTrendingArtist->toArray());
        $dbTrendingArtist = $this->trendingArtistRepo->find($trendingArtist->id);
        $this->assertModelData($fakeTrendingArtist, $dbTrendingArtist->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteTrendingArtist()
    {
        $trendingArtist = $this->makeTrendingArtist();
        $resp = $this->trendingArtistRepo->delete($trendingArtist->id);
        $this->assertTrue($resp);
        $this->assertNull(TrendingArtist::find($trendingArtist->id), 'TrendingArtist should not exist in DB');
    }
}
