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
use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Connector;
use InvalidArgumentException;

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
    public function getConnectors(): array
    {
        return $this->connectors->toArray();
    }

    /**
     * Get a connector based on the configIg.
     *
     * @param int $configId
     *
     * @return Connector
     */
    public function getConnector(int $configId): Connector
    {
        foreach ($this->connectors as $connector) {
            if ($configId === $connector->getConfigId()) {
                return $connector;
            }
        }

        throw new InvalidArgumentException(sprintf('No connector found with configId %d', $configId));
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
        if (!$this->hasConnector($connector->getConfigId())) {
            $this->connectors->add($connector);
        }

        return $this;
    }

    /**
     * @param int $configId
     *
     * @return bool
     */
    public function hasConnector(int $configId): bool
    {
        return $this->connectors->exists(function ($key, Connector $element) use ($configId) {
            return $configId === $element->getConfigId();
        });
    }

    /**
     * @param Connector $connector
     *
     * @return bool true if this collection contained the specified element, false otherwise
     */
    public function removeConnector(Connector $connector): bool
    {
        return $this->connectors->removeElement($connector);
    }
}
