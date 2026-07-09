<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewApiTest extends TestCase
{
    use MakeViewTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateView()
    {
        $view = $this->fakeViewData();
        $this->json('POST', '/api/v1/views', $view);

        $this->assertApiResponse($view);
    }

    /**
     * @test
     */
    public function testReadView()
    {
        $view = $this->makeView();
        $this->json('GET', '/api/v1/views/'.$view->id);

        $this->assertApiResponse($view->toArray());
    }

    /**
     * @test
     */
    public function testUpdateView()
    {
        $view = $this->makeView();
        $editedView = $this->fakeViewData();

        $this->json('PUT', '/api/v1/views/'.$view->id, $editedView);

        $this->assertApiResponse($editedView);
    }

    /**
     * @test
     */
    public function testDeleteView()
    {
        $view = $this->makeView();
        $this->json('DELETE', '/api/v1/views/'.$view->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/views/'.$view->id);

        $this->assertResponseStatus(404);
    }
}
