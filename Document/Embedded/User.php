<?php

/*
* This file is part of the Integrated package.
*
* (c) e-Active B.V. <integrated@e-active.nl>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Integrated\Bundle\ContentHistoryBundle\Document\Embedded;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @author Ger Jan van den Bosch <gerjan@e-active.nl>
 *
 * @ODM\EmbeddedDocument
 */
class User
{
    /**
     * @var int
     * @ODM\Int
     * @ODM\Index
     */
    protected $id;

    /**
     * @var string
     * @ODM\String
     */
    protected $name;

    /**
     * @var string
     * @ODM\String
     */
    protected $ipAddress;

    /**
     * @var string
     * @ODM\String
     */
    protected $endpoint;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
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
    public function getName()
    {
        return $this->name;
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
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @param string $ipAddress
     * @return $this
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    /**
     * Get the endpoint (e.g. the request URL or console command name).
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param string $endpoint
     * @return $this
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
        return $this;
    }
}
