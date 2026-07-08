<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserSubscriptionApiTest extends TestCase
{
    use MakeUserSubscriptionTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateUserSubscription()
    {
        $userSubscription = $this->fakeUserSubscriptionData();
        $this->json('POST', '/api/v1/userSubscriptions', $userSubscription);

        $this->assertApiResponse($userSubscription);
    }

    /**
     * @test
     */
    public function testReadUserSubscription()
    {
        $userSubscription = $this->makeUserSubscription();
        $this->json('GET', '/api/v1/userSubscriptions/'.$userSubscription->id);

        $this->assertApiResponse($userSubscription->toArray());
    }

    /**
     * @test
     */
    public function testUpdateUserSubscription()
    {
        $userSubscription = $this->makeUserSubscription();
        $editedUserSubscription = $this->fakeUserSubscriptionData();

        $this->json('PUT', '/api/v1/userSubscriptions/'.$userSubscription->id, $editedUserSubscription);

        $this->assertApiResponse($editedUserSubscription);
    }

    /**
     * @test
     */
    public function testDeleteUserSubscription()
    {
        $userSubscription = $this->makeUserSubscription();
        $this->json('DELETE', '/api/v1/userSubscriptions/'.$userSubscription->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/userSubscriptions/'.$userSubscription->id);

        $this->assertResponseStatus(404);
    }
}
