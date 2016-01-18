<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\PageBundle\Document\Page;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;
use Integrated\Bundle\ContentBundle\Document\Channel\Channel;

/**
 * @author Johan Liefers <johan@e-active.nl>
 *
 * @ODM\Document(collection="content_type_page")
 */
class ContentTypePage
{
    /**
     * @var string
     * @ODM\Id(strategy="UUID")
     */
    protected $id;

    /**
     * @var string
     * @ODM\String
     * @Assert\NotBlank
     * @Assert\Regex(
     *     pattern="/{slug}/",
     *     message="Url must contain {slug}"
     * )
     */
    protected $path;

    /**
     * @var string
     * @ODM\String
     * @Assert\NotBlank
     */
    protected $layout;

    /**
     * @var \DateTime
     * @ODM\Date
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @ODM\Date
     */
    protected $updatedAt;

    /**
     * @var bool
     * @ODM\Boolean
     */
    protected $disabled = false;

    /**
     * @var Channel
     * @ODM\ReferenceOne(targetDocument="Integrated\Bundle\ContentBundle\Document\Channel\Channel")
     */
    protected $channel;

    /**
     * @var ContentType
     * @ODM\ReferenceOne(targetDocument="Integrated\Bundle\ContentBundle\Document\ContentType\ContentType")
     */
    protected $contentType;

    /**
     * @param ContentType $contentType
     * @param Channel $channel
     * @param string $layout
     * @param null $path
     */
    public function __construct(ContentType $contentType, Channel $channel, $layout = 'default.html.twig', $path = null)
    {
        $this->setContentType($contentType);
        $this->setChannel($channel);
        $this->setLayout($layout);
        if ($path) {
            $this->setPath($path);
        } else {
            $this->setPath(sprintf('/content/%s/{slug}', $contentType->getId()));
        }

        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param string $layout
     * @return $this
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * @return ContentType
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param ContentType $contentType
     * @return $this
     */
    public function setContentType(ContentType $contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    /**
     * @param bool $disabled
     * @return $this
     */
    public function setDisabled($disabled)
    {
        $this->disabled = (bool) $disabled;
        return $this;
    }

    /**
     * @return Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param Channel $channel
     * @return $this
     */
    public function setChannel(Channel $channel)
    {
        $this->channel = $channel;
        return $this;
    }
}
