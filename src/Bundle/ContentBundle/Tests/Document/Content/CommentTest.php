<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Tests\Document\Content;

use Integrated\Bundle\ContentBundle\Document\Content\Comment;

/**
 * @author Koen Prins <koen@e-active.nl>
 */
class CommentTest extends ContentTest
{
    /**
     * @var comment
     */
    private $comment;

    /**
     * Setup the test.
     */
    protected function setUp(): void
    {
        $this->comment = new Comment();
    }

    /**
     * Test get- and setTitle function.
     */
    public function testGetAndSetTitleFunction()
    {
        $title = 'title';
        $this->assertSame($title, $this->comment->setTitle($title)->getTitle());
    }

    /**
     * Test get- and setName function.
     */
    public function testGetAndSetNameFunction()
    {
        $name = 'name';
        $this->assertSame($name, $this->comment->setTitle($name)->getTitle());
    }

    /**
     * Test get- and setEmail function.
     */
    public function testGetAndSetEmailFunction()
    {
        $email = 'email';
        $this->assertSame($email, $this->comment->setTitle($email)->getTitle());
    }

    /**
     * Test get- and setEmail function.
     */
    public function testGetAndSetCommentFunction()
    {
        $comment = 'comment';
        $this->assertSame($comment, $this->comment->setTitle($comment)->getTitle());
    }

    /**
     * Test toString function.
     */
    public function testToStringFunction()
    {
        $title = 'Title';
        $this->assertEquals($title, (string) $this->comment->setTitle($title));
    }

    /**
     * {@inheritdoc}
     */
    protected function getContent()
    {
        return $this->comment;
    }
}
