<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PackageApiTest extends TestCase
{
    use MakePackageTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePackage()
    {
        $package = $this->fakePackageData();
        $this->json('POST', '/api/v1/packages', $package);

        $this->assertApiResponse($package);
    }

    /**
     * @test
     */
    public function testReadPackage()
    {
        $package = $this->makePackage();
        $this->json('GET', '/api/v1/packages/'.$package->id);

        $this->assertApiResponse($package->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePackage()
    {
        $package = $this->makePackage();
        $editedPackage = $this->fakePackageData();

        $this->json('PUT', '/api/v1/packages/'.$package->id, $editedPackage);

        $this->assertApiResponse($editedPackage);
    }

    /**
     * @test
     */
    public function testDeletePackage()
    {
        $package = $this->makePackage();
        $this->json('DELETE', '/api/v1/packages/'.$package->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/packages/'.$package->id);

        $this->assertResponseStatus(404);
    }
}
