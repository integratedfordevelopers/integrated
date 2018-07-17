<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ChannelBundle\Document\Embedded;

class Connector
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $adapter;

    /**
     * @var string
     */
    protected $externalId;

    /**
     * Get the id of the document.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the id of the document.
     *
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = (int) $id;

        return $this;
    }

    /**
     * Get the adapter of the document.
     *
     * @return string
     */
    public function getAdapter(): string
    {
        return $this->adapter;
    }

    /**
     * Set the adapter of the document.
     *
     * @param string $adapter
     *
     * @return $this
     */
    public function setAdapter($adapter)
    {
        $this->adapter = (string) $adapter;

        return $this;
    }

    /**
     * Get the externalId of the document.
     *
     * @return string
     */
    public function getExternalId(): string
    {
        return $this->externalId;
    }

    /**
     * Set the externalId of the document.
     *
     * @param string $externalId
     *
     * @return $this
     */
    public function setExternalId($externalId)
    {
        $this->externalId = (string) $externalId;

        return $this;
    }
}
