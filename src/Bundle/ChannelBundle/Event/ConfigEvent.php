<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ChannelBundle\Event;

use Integrated\Bundle\ChannelBundle\Model\Config;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ConfigEvent extends Event
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param Config  $config
     * @param Request $request
     */
    public function __construct(Config $config, Request $request)
    {
        $this->config = $config;
        $this->request = $request;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
