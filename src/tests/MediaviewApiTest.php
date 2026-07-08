<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MediaviewApiTest extends TestCase
{
    use MakeMediaviewTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateMediaview()
    {
        $mediaview = $this->fakeMediaviewData();
        $this->json('POST', '/api/v1/mediaviews', $mediaview);

        $this->assertApiResponse($mediaview);
    }

    /**
     * @test
     */
    public function testReadMediaview()
    {
        $mediaview = $this->makeMediaview();
        $this->json('GET', '/api/v1/mediaviews/'.$mediaview->id);

        $this->assertApiResponse($mediaview->toArray());
    }

    /**
     * @test
     */
    public function testUpdateMediaview()
    {
        $mediaview = $this->makeMediaview();
        $editedMediaview = $this->fakeMediaviewData();

        $this->json('PUT', '/api/v1/mediaviews/'.$mediaview->id, $editedMediaview);

        $this->assertApiResponse($editedMediaview);
    }

    /**
     * @test
     */
    public function testDeleteMediaview()
    {
        $mediaview = $this->makeMediaview();
        $this->json('DELETE', '/api/v1/mediaviews/'.$mediaview->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/mediaviews/'.$mediaview->id);

        $this->assertResponseStatus(404);
    }
}
