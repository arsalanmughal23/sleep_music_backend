<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CommentApiTest extends TestCase
{
    use MakeCommentTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateComment()
    {
        $comment = $this->fakeCommentData();
        $this->json('POST', '/api/v1/comments', $comment);

        $this->assertApiResponse($comment);
    }

    /**
     * @test
     */
    public function testReadComment()
    {
        $comment = $this->makeComment();
        $this->json('GET', '/api/v1/comments/'.$comment->id);

        $this->assertApiResponse($comment->toArray());
    }

    /**
     * @test
     */
    public function testUpdateComment()
    {
        $comment = $this->makeComment();
        $editedComment = $this->fakeCommentData();

        $this->json('PUT', '/api/v1/comments/'.$comment->id, $editedComment);

        $this->assertApiResponse($editedComment);
    }

    /**
     * @test
     */
    public function testDeleteComment()
    {
        $comment = $this->makeComment();
        $this->json('DELETE', '/api/v1/comments/'.$comment->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/comments/'.$comment->id);

        $this->assertResponseStatus(404);
    }
}
