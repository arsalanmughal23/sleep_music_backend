<?php

use App\Models\Comment;
use App\Repositories\Admin\CommentRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CommentRepositoryTest extends TestCase
{
    use MakeCommentTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CommentRepository
     */
    protected $commentRepo;

    public function setUp()
    {
        parent::setUp();
        $this->commentRepo = App::make(CommentRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateComment()
    {
        $comment = $this->fakeCommentData();
        $createdComment = $this->commentRepo->create($comment);
        $createdComment = $createdComment->toArray();
        $this->assertArrayHasKey('id', $createdComment);
        $this->assertNotNull($createdComment['id'], 'Created Comment must have id specified');
        $this->assertNotNull(Comment::find($createdComment['id']), 'Comment with given id must be in DB');
        $this->assertModelData($comment, $createdComment);
    }

    /**
     * @test read
     */
    public function testReadComment()
    {
        $comment = $this->makeComment();
        $dbComment = $this->commentRepo->find($comment->id);
        $dbComment = $dbComment->toArray();
        $this->assertModelData($comment->toArray(), $dbComment);
    }

    /**
     * @test update
     */
    public function testUpdateComment()
    {
        $comment = $this->makeComment();
        $fakeComment = $this->fakeCommentData();
        $updatedComment = $this->commentRepo->update($fakeComment, $comment->id);
        $this->assertModelData($fakeComment, $updatedComment->toArray());
        $dbComment = $this->commentRepo->find($comment->id);
        $this->assertModelData($fakeComment, $dbComment->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteComment()
    {
        $comment = $this->makeComment();
        $resp = $this->commentRepo->delete($comment->id);
        $this->assertTrue($resp);
        $this->assertNull(Comment::find($comment->id), 'Comment should not exist in DB');
    }
}
