<?php

use App\Models\NotificationUser;
use App\Repositories\Admin\NotificationUserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NotificationUserRepositoryTest extends TestCase
{
    use MakeNotificationUserTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var NotificationUserRepository
     */
    protected $notificationUserRepo;

    public function setUp()
    {
        parent::setUp();
        $this->notificationUserRepo = App::make(NotificationUserRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateNotificationUser()
    {
        $notificationUser = $this->fakeNotificationUserData();
        $createdNotificationUser = $this->notificationUserRepo->create($notificationUser);
        $createdNotificationUser = $createdNotificationUser->toArray();
        $this->assertArrayHasKey('id', $createdNotificationUser);
        $this->assertNotNull($createdNotificationUser['id'], 'Created NotificationUser must have id specified');
        $this->assertNotNull(NotificationUser::find($createdNotificationUser['id']), 'NotificationUser with given id must be in DB');
        $this->assertModelData($notificationUser, $createdNotificationUser);
    }

    /**
     * @test read
     */
    public function testReadNotificationUser()
    {
        $notificationUser = $this->makeNotificationUser();
        $dbNotificationUser = $this->notificationUserRepo->find($notificationUser->id);
        $dbNotificationUser = $dbNotificationUser->toArray();
        $this->assertModelData($notificationUser->toArray(), $dbNotificationUser);
    }

    /**
     * @test update
     */
    public function testUpdateNotificationUser()
    {
        $notificationUser = $this->makeNotificationUser();
        $fakeNotificationUser = $this->fakeNotificationUserData();
        $updatedNotificationUser = $this->notificationUserRepo->update($fakeNotificationUser, $notificationUser->id);
        $this->assertModelData($fakeNotificationUser, $updatedNotificationUser->toArray());
        $dbNotificationUser = $this->notificationUserRepo->find($notificationUser->id);
        $this->assertModelData($fakeNotificationUser, $dbNotificationUser->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteNotificationUser()
    {
        $notificationUser = $this->makeNotificationUser();
        $resp = $this->notificationUserRepo->delete($notificationUser->id);
        $this->assertTrue($resp);
        $this->assertNull(NotificationUser::find($notificationUser->id), 'NotificationUser should not exist in DB');
    }
}
