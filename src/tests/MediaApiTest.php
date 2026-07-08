<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MediaApiTest extends TestCase
{
    use MakeMediaTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateMedia()
    {
        $media = $this->fakeMediaData();
        $this->json('POST', '/api/v1/media', $media);

        $this->assertApiResponse($media);
    }

    /**
     * @test
     */
    public function testReadMedia()
    {
        $media = $this->makeMedia();
        $this->json('GET', '/api/v1/media/'.$media->id);

        $this->assertApiResponse($media->toArray());
    }

    /**
     * @test
     */
    public function testUpdateMedia()
    {
        $media = $this->makeMedia();
        $editedMedia = $this->fakeMediaData();

        $this->json('PUT', '/api/v1/media/'.$media->id, $editedMedia);

        $this->assertApiResponse($editedMedia);
    }

    /**
     * @test
     */
    public function testDeleteMedia()
    {
        $media = $this->makeMedia();
        $this->json('DELETE', '/api/v1/media/'.$media->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/media/'.$media->id);

        $this->assertResponseStatus(404);
    }
}
