<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentBundle\Document\Channel;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

use Integrated\Common\Content\ChannelInterface;

/**
 * Channel document
 *
 * @author Jeroen van Leeuwen <jeroen@e-active.nl>
 *
 * @ODM\Document(collection="channels")
 */
class Channel implements ChannelInterface
{
    /**
     * @var string
     * @ODM\Id(strategy="UUID")
     */
    protected $id;

    /**
     * @var string the name of the channel
     * @ODM\String
     * @ODM\Index
     */
    protected $name;

    /**
     * @var array
     * @ODM\Collection
     */
    protected $domains;

    /**
     * @var \DateTime
     * @ODM\Date
     */
    protected $createdAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Get the id of the channel
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the id of the channel
     *
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param array $domains
     * @return $this
     */
    public function setDomains(array $domains)
    {
        $this->domains = $domains;
        return $this;
    }

    /**
     * @return array
     */
    public function getDomains()
    {
        return $this->domains;
    }

    /**
     * Get the createdAt of the channel
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the createdAt of the channel
     *
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}