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

use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Connector;

interface ConnectorInterface
{
    /**
     * Add author to Connectors collection.
     *
     * @param Connector $connector
     *
     * @return $this
     */
    public function addConnector(Connector $connector);

    /**
     * @param int $configId
     *
     * @return bool
     */
    public function hasConnector(int $configId): bool;

    /**
     * @param int $configId
     *
     * @return Connector
     */
    public function getConnector(int $configId): Connector;
}
