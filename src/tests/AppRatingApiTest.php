<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AppRatingApiTest extends TestCase
{
    use MakeAppRatingTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAppRating()
    {
        $appRating = $this->fakeAppRatingData();
        $this->json('POST', '/api/v1/appRatings', $appRating);

        $this->assertApiResponse($appRating);
    }

    /**
     * @test
     */
    public function testReadAppRating()
    {
        $appRating = $this->makeAppRating();
        $this->json('GET', '/api/v1/appRatings/'.$appRating->id);

        $this->assertApiResponse($appRating->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAppRating()
    {
        $appRating = $this->makeAppRating();
        $editedAppRating = $this->fakeAppRatingData();

        $this->json('PUT', '/api/v1/appRatings/'.$appRating->id, $editedAppRating);

        $this->assertApiResponse($editedAppRating);
    }

    /**
     * @test
     */
    public function testDeleteAppRating()
    {
        $appRating = $this->makeAppRating();
        $this->json('DELETE', '/api/v1/appRatings/'.$appRating->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/appRatings/'.$appRating->id);

        $this->assertResponseStatus(404);
    }
}
