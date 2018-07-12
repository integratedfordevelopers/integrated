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

use Integrated\Bundle\ChannelBundle\Document\Embedded\Connector;

interface ConnectorInterface
{
    /**
     * Get the connectors of the document.
     *
     * @return array
     */
    public function getConnectors();

    /**
     * Set the connectors of the document.
     *
     * @param array $connectors
     *
     * @return $this
     */
    public function setConnectors(array $connectors);

    /**
     * Add author to Connectors collection.
     *
     * @param Connector $connector
     *
     * @return $this
     */
    public function addConnector(Connector $connector);

    /**
     * @param int $connectorId
     *
     * @return bool
     */
    public function hasConnector($connectorId);

    /**
     * @param Connector $connector
     *
     * @return bool true if this collection contained the specified element, false otherwise
     */
    public function removeConnector(Connector $connector);
}
