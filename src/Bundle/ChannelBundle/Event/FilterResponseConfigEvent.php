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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class FilterResponseConfigEvent extends ConfigEvent
{
    /**
     * @var Response
     */
    private $response;

    /**
     * @param Config   $config
     * @param Request  $request
     * @param Response $response
     */
    public function __construct(Config $config, Request $request, Response $response)
    {
        parent::__construct($config, $request);

        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
