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
    protected $connectorId;

    /**
     * @var string
     */
    protected $connectorAdapter;

    /**
     * @var string
     */
    protected $externalId;

    /**
     * Get the connectorId of the document.
     *
     * @return int
     */
    public function getConnectorId()
    {
        return $this->connectorId;
    }

    /**
     * Set the connectorId of the document.
     *
     * @param int $connectorId
     * @return $this
     */
    public function setConnectorId($connectorId)
    {
        $this->connectorId = (int)$connectorId;
        return $this;
    }

    /**
     * Get the connectorAdapter of the document.
     *
     * @return string
     */
    public function getConnectorAdapter()
    {
        return $this->connectorAdapter;
    }

    /**
     * Set the connectorAdapter of the document.
     *
     * @param string $connectorAdapter
     * @return $this
     */
    public function setConnectorAdapter($connectorAdapter)
    {
        $this->connectorAdapter = (string)$connectorAdapter;
        return $this;
    }

    /**
     * Get the externalId of the document.
     *
     * @return int
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * Set the externalId of the document.
     *
     * @param string $externalId
     * @return $this
     */
    public function setExternalId($externalId)
    {
        $this->externalId = (string)$externalId;
        return $this;
    }
}
