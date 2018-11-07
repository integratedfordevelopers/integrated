<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\Document\Content\Embedded;

class Connector
{
    /**
     * @var int
     */
    protected $configId;

    /**
     * @var string
     */
    protected $configAdapter;

    /**
     * @var string
     */
    protected $externalId;

    /**
     * Get the configId of the document.
     *
     * @return int
     */
    public function getConfigId(): int
    {
        return $this->configId;
    }

    /**
     * Set the configId of the document.
     *
     * @param int $configId
     *
     * @return $this
     */
    public function setConfigId(int $configId)
    {
        $this->configId = $configId;

        return $this;
    }

    /**
     * Get the configAdapter of the document.
     *
     * @return string
     */
    public function getConfigAdapter(): string
    {
        return $this->configAdapter;
    }

    /**
     * Set the configAdapter of the document.
     *
     * @param string $configAdapter
     *
     * @return $this
     */
    public function setConfigAdapter(string $configAdapter)
    {
        $this->configAdapter = $configAdapter;

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
    public function setExternalId(string $externalId)
    {
        $this->externalId = $externalId;

        return $this;
    }
}
