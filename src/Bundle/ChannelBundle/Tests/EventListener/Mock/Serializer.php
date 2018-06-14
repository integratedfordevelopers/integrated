<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ChannelBundle\Tests\EventListener\Mock;

use Integrated\Common\Channel\Exporter\Queue\Request;
use Integrated\Common\Channel\Exporter\Queue\RequestSerializerInterface;

class Serializer implements RequestSerializerInterface
{
    /**
     * {@inheritdoc}
     */
    public function serialize(Request $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function deserialize($data)
    {
        return $data;
    }
}
