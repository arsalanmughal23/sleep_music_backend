<?php

use App\Models\Card;
use App\Repositories\Admin\CardRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CardRepositoryTest extends TestCase
{
    use MakeCardTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CardRepository
     */
    protected $cardRepo;

    public function setUp()
    {
        parent::setUp();
        $this->cardRepo = App::make(CardRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCard()
    {
        $card = $this->fakeCardData();
        $createdCard = $this->cardRepo->create($card);
        $createdCard = $createdCard->toArray();
        $this->assertArrayHasKey('id', $createdCard);
        $this->assertNotNull($createdCard['id'], 'Created Card must have id specified');
        $this->assertNotNull(Card::find($createdCard['id']), 'Card with given id must be in DB');
        $this->assertModelData($card, $createdCard);
    }

    /**
     * @test read
     */
    public function testReadCard()
    {
        $card = $this->makeCard();
        $dbCard = $this->cardRepo->find($card->id);
        $dbCard = $dbCard->toArray();
        $this->assertModelData($card->toArray(), $dbCard);
    }

    /**
     * @test update
     */
    public function testUpdateCard()
    {
        $card = $this->makeCard();
        $fakeCard = $this->fakeCardData();
        $updatedCard = $this->cardRepo->update($fakeCard, $card->id);
        $this->assertModelData($fakeCard, $updatedCard->toArray());
        $dbCard = $this->cardRepo->find($card->id);
        $this->assertModelData($fakeCard, $dbCard->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCard()
    {
        $card = $this->makeCard();
        $resp = $this->cardRepo->delete($card->id);
        $this->assertTrue($resp);
        $this->assertNull(Card::find($card->id), 'Card should not exist in DB');
    }
}
