<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NotificationUserApiTest extends TestCase
{
    use MakeNotificationUserTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateNotificationUser()
    {
        $notificationUser = $this->fakeNotificationUserData();
        $this->json('POST', '/api/v1/notificationUsers', $notificationUser);

        $this->assertApiResponse($notificationUser);
    }

    /**
     * @test
     */
    public function testReadNotificationUser()
    {
        $notificationUser = $this->makeNotificationUser();
        $this->json('GET', '/api/v1/notificationUsers/'.$notificationUser->id);

        $this->assertApiResponse($notificationUser->toArray());
    }

    /**
     * @test
     */
    public function testUpdateNotificationUser()
    {
        $notificationUser = $this->makeNotificationUser();
        $editedNotificationUser = $this->fakeNotificationUserData();

        $this->json('PUT', '/api/v1/notificationUsers/'.$notificationUser->id, $editedNotificationUser);

        $this->assertApiResponse($editedNotificationUser);
    }

    /**
     * @test
     */
    public function testDeleteNotificationUser()
    {
        $notificationUser = $this->makeNotificationUser();
        $this->json('DELETE', '/api/v1/notificationUsers/'.$notificationUser->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/notificationUsers/'.$notificationUser->id);

        $this->assertResponseStatus(404);
    }
}
