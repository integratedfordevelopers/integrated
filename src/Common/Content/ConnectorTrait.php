<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Common\Content;

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\ChannelBundle\Document\Embedded\Connector;

trait ConnectorTrait
{
    /**
     * @var ArrayCollection|Connector[]
     */
    protected $connectors;

    /**
     * Get the connectors of the document.
     *
     * @return array
     */
    public function getConnectors()
    {
        return $this->connectors->toArray();
    }

    /**
     * Set the connectors of the document.
     *
     * @param array $connectors
     *
     * @return $this
     */
    public function setConnectors(array $connectors)
    {
        $this->connectors = new ArrayCollection($connectors);

        return $this;
    }

    /**
     * Add author to Connectors collection.
     *
     * @param Connector $connector
     *
     * @return $this
     */
    public function addConnector(Connector $connector)
    {
        if (!$this->hasConnector($connector->getConnectorId())) {
            $this->connectors->add($connector);
        }

        return $this;
    }

    /**
     * @param int $connectorId
     *
     * @return bool
     */
    public function hasConnector($connectorId)
    {
        return $this->connectors->exists(function ($key, Connector $element) use ($connectorId) {
            return $connectorId === $element->getConnectorId();
        });
    }

    /**
     * @param Connector $connector
     *
     * @return bool true if this collection contained the specified element, false otherwise
     */
    public function removeConnector(Connector $connector)
    {
        return $this->connectors->removeElement($connector);
    }
}
