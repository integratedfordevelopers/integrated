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

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class GetResponseConfigEvent extends ConfigEvent
{
    /**
     * @var Response
     */
    private $response = null;

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response = null)
    {
        $this->response = $response;
    }
}
