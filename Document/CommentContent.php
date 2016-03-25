<?php

namespace Integrated\Bundle\CommentBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Integrated\Bundle\ContentBundle\Document\Content\Content;

/**
 * Class CommentContent
 *
 * @ODM\Document
 */
class CommentContent
{
    /**
     * @var string
     * @ODM\Id(strategy="UUID")
     */
    protected $id;

    /**
     * @var Content
     * @ODM\EmbedOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Content\Content")
     */
    protected $content;

    /**
     * @var string
     * @ODM\String()
     */
    protected $body;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param Content $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }
}
