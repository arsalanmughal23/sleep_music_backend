<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DeleteTypeApiTest extends TestCase
{
    use MakeDeleteTypeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDeleteType()
    {
        $deleteType = $this->fakeDeleteTypeData();
        $this->json('POST', '/api/v1/deleteTypes', $deleteType);

        $this->assertApiResponse($deleteType);
    }

    /**
     * @test
     */
    public function testReadDeleteType()
    {
        $deleteType = $this->makeDeleteType();
        $this->json('GET', '/api/v1/deleteTypes/'.$deleteType->id);

        $this->assertApiResponse($deleteType->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDeleteType()
    {
        $deleteType = $this->makeDeleteType();
        $editedDeleteType = $this->fakeDeleteTypeData();

        $this->json('PUT', '/api/v1/deleteTypes/'.$deleteType->id, $editedDeleteType);

        $this->assertApiResponse($editedDeleteType);
    }

    /**
     * @test
     */
    public function testDeleteDeleteType()
    {
        $deleteType = $this->makeDeleteType();
        $this->json('DELETE', '/api/v1/deleteTypes/'.$deleteType->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/deleteTypes/'.$deleteType->id);

        $this->assertResponseStatus(404);
    }
}
