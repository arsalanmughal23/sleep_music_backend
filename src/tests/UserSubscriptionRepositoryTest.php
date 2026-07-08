<?php

use App\Models\UserSubscription;
use App\Repositories\Admin\UserSubscriptionRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserSubscriptionRepositoryTest extends TestCase
{
    use MakeUserSubscriptionTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var UserSubscriptionRepository
     */
    protected $userSubscriptionRepo;

    public function setUp()
    {
        parent::setUp();
        $this->userSubscriptionRepo = App::make(UserSubscriptionRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateUserSubscription()
    {
        $userSubscription = $this->fakeUserSubscriptionData();
        $createdUserSubscription = $this->userSubscriptionRepo->create($userSubscription);
        $createdUserSubscription = $createdUserSubscription->toArray();
        $this->assertArrayHasKey('id', $createdUserSubscription);
        $this->assertNotNull($createdUserSubscription['id'], 'Created UserSubscription must have id specified');
        $this->assertNotNull(UserSubscription::find($createdUserSubscription['id']), 'UserSubscription with given id must be in DB');
        $this->assertModelData($userSubscription, $createdUserSubscription);
    }

    /**
     * @test read
     */
    public function testReadUserSubscription()
    {
        $userSubscription = $this->makeUserSubscription();
        $dbUserSubscription = $this->userSubscriptionRepo->find($userSubscription->id);
        $dbUserSubscription = $dbUserSubscription->toArray();
        $this->assertModelData($userSubscription->toArray(), $dbUserSubscription);
    }

    /**
     * @test update
     */
    public function testUpdateUserSubscription()
    {
        $userSubscription = $this->makeUserSubscription();
        $fakeUserSubscription = $this->fakeUserSubscriptionData();
        $updatedUserSubscription = $this->userSubscriptionRepo->update($fakeUserSubscription, $userSubscription->id);
        $this->assertModelData($fakeUserSubscription, $updatedUserSubscription->toArray());
        $dbUserSubscription = $this->userSubscriptionRepo->find($userSubscription->id);
        $this->assertModelData($fakeUserSubscription, $dbUserSubscription->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteUserSubscription()
    {
        $userSubscription = $this->makeUserSubscription();
        $resp = $this->userSubscriptionRepo->delete($userSubscription->id);
        $this->assertTrue($resp);
        $this->assertNull(UserSubscription::find($userSubscription->id), 'UserSubscription should not exist in DB');
    }
}
