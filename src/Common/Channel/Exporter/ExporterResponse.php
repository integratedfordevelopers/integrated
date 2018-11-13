<?php

namespace Integrated\Common\Channel\Exporter;

class ExporterResponse
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
     * @param int    $configId
     * @param string $configAdapter
     */
    public function __construct(int $configId, string $configAdapter)
    {
        $this->configId = $configId;
        $this->configAdapter = $configAdapter;
    }

    /**
     * @return int
     */
    public function getConfigId(): int
    {
        return $this->configId;
    }

    /**
     * @return string
     */
    public function getConfigAdapter(): string
    {
        return $this->configAdapter;
    }

    /**
     * @return string
     */
    public function getExternalId(): string
    {
        return $this->externalId;
    }

    /**
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
