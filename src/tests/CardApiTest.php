<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CardApiTest extends TestCase
{
    use MakeCardTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCard()
    {
        $card = $this->fakeCardData();
        $this->json('POST', '/api/v1/cards', $card);

        $this->assertApiResponse($card);
    }

    /**
     * @test
     */
    public function testReadCard()
    {
        $card = $this->makeCard();
        $this->json('GET', '/api/v1/cards/'.$card->id);

        $this->assertApiResponse($card->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCard()
    {
        $card = $this->makeCard();
        $editedCard = $this->fakeCardData();

        $this->json('PUT', '/api/v1/cards/'.$card->id, $editedCard);

        $this->assertApiResponse($editedCard);
    }

    /**
     * @test
     */
    public function testDeleteCard()
    {
        $card = $this->makeCard();
        $this->json('DELETE', '/api/v1/cards/'.$card->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/cards/'.$card->id);

        $this->assertResponseStatus(404);
    }
}
