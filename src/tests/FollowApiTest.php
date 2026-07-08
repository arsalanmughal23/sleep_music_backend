<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FollowApiTest extends TestCase
{
    use MakeFollowTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateFollow()
    {
        $follow = $this->fakeFollowData();
        $this->json('POST', '/api/v1/follows', $follow);

        $this->assertApiResponse($follow);
    }

    /**
     * @test
     */
    public function testReadFollow()
    {
        $follow = $this->makeFollow();
        $this->json('GET', '/api/v1/follows/'.$follow->id);

        $this->assertApiResponse($follow->toArray());
    }

    /**
     * @test
     */
    public function testUpdateFollow()
    {
        $follow = $this->makeFollow();
        $editedFollow = $this->fakeFollowData();

        $this->json('PUT', '/api/v1/follows/'.$follow->id, $editedFollow);

        $this->assertApiResponse($editedFollow);
    }

    /**
     * @test
     */
    public function testDeleteFollow()
    {
        $follow = $this->makeFollow();
        $this->json('DELETE', '/api/v1/follows/'.$follow->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/follows/'.$follow->id);

        $this->assertResponseStatus(404);
    }
}
