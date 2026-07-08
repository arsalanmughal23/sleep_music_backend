<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TrendingArtistApiTest extends TestCase
{
    use MakeTrendingArtistTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateTrendingArtist()
    {
        $trendingArtist = $this->fakeTrendingArtistData();
        $this->json('POST', '/api/v1/trendingArtists', $trendingArtist);

        $this->assertApiResponse($trendingArtist);
    }

    /**
     * @test
     */
    public function testReadTrendingArtist()
    {
        $trendingArtist = $this->makeTrendingArtist();
        $this->json('GET', '/api/v1/trendingArtists/'.$trendingArtist->id);

        $this->assertApiResponse($trendingArtist->toArray());
    }

    /**
     * @test
     */
    public function testUpdateTrendingArtist()
    {
        $trendingArtist = $this->makeTrendingArtist();
        $editedTrendingArtist = $this->fakeTrendingArtistData();

        $this->json('PUT', '/api/v1/trendingArtists/'.$trendingArtist->id, $editedTrendingArtist);

        $this->assertApiResponse($editedTrendingArtist);
    }

    /**
     * @test
     */
    public function testDeleteTrendingArtist()
    {
        $trendingArtist = $this->makeTrendingArtist();
        $this->json('DELETE', '/api/v1/trendingArtists/'.$trendingArtist->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/trendingArtists/'.$trendingArtist->id);

        $this->assertResponseStatus(404);
    }
}
