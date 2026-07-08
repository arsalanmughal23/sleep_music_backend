<?php

use App\Models\Package;
use App\Repositories\Admin\PackageRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PackageRepositoryTest extends TestCase
{
    use MakePackageTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PackageRepository
     */
    protected $packageRepo;

    public function setUp()
    {
        parent::setUp();
        $this->packageRepo = App::make(PackageRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePackage()
    {
        $package = $this->fakePackageData();
        $createdPackage = $this->packageRepo->create($package);
        $createdPackage = $createdPackage->toArray();
        $this->assertArrayHasKey('id', $createdPackage);
        $this->assertNotNull($createdPackage['id'], 'Created Package must have id specified');
        $this->assertNotNull(Package::find($createdPackage['id']), 'Package with given id must be in DB');
        $this->assertModelData($package, $createdPackage);
    }

    /**
     * @test read
     */
    public function testReadPackage()
    {
        $package = $this->makePackage();
        $dbPackage = $this->packageRepo->find($package->id);
        $dbPackage = $dbPackage->toArray();
        $this->assertModelData($package->toArray(), $dbPackage);
    }

    /**
     * @test update
     */
    public function testUpdatePackage()
    {
        $package = $this->makePackage();
        $fakePackage = $this->fakePackageData();
        $updatedPackage = $this->packageRepo->update($fakePackage, $package->id);
        $this->assertModelData($fakePackage, $updatedPackage->toArray());
        $dbPackage = $this->packageRepo->find($package->id);
        $this->assertModelData($fakePackage, $dbPackage->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePackage()
    {
        $package = $this->makePackage();
        $resp = $this->packageRepo->delete($package->id);
        $this->assertTrue($resp);
        $this->assertNull(Package::find($package->id), 'Package should not exist in DB');
    }
}
