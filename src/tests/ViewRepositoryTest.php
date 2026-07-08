<?php

use App\Models\View;
use App\Repositories\Admin\ViewRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewRepositoryTest extends TestCase
{
    use MakeViewTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ViewRepository
     */
    protected $viewRepo;

    public function setUp()
    {
        parent::setUp();
        $this->viewRepo = App::make(ViewRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateView()
    {
        $view = $this->fakeViewData();
        $createdView = $this->viewRepo->create($view);
        $createdView = $createdView->toArray();
        $this->assertArrayHasKey('id', $createdView);
        $this->assertNotNull($createdView['id'], 'Created View must have id specified');
        $this->assertNotNull(View::find($createdView['id']), 'View with given id must be in DB');
        $this->assertModelData($view, $createdView);
    }

    /**
     * @test read
     */
    public function testReadView()
    {
        $view = $this->makeView();
        $dbView = $this->viewRepo->find($view->id);
        $dbView = $dbView->toArray();
        $this->assertModelData($view->toArray(), $dbView);
    }

    /**
     * @test update
     */
    public function testUpdateView()
    {
        $view = $this->makeView();
        $fakeView = $this->fakeViewData();
        $updatedView = $this->viewRepo->update($fakeView, $view->id);
        $this->assertModelData($fakeView, $updatedView->toArray());
        $dbView = $this->viewRepo->find($view->id);
        $this->assertModelData($fakeView, $dbView->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteView()
    {
        $view = $this->makeView();
        $resp = $this->viewRepo->delete($view->id);
        $this->assertTrue($resp);
        $this->assertNull(View::find($view->id), 'View should not exist in DB');
    }
}
