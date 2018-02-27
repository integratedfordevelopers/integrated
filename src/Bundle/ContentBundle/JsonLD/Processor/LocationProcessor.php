<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\JsonLD\Processor;

use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Location;
use Integrated\Common\Normalizer\ContainerInterface;
use Integrated\Common\Normalizer\Processor\Context;
use Integrated\Common\Normalizer\Processor\ProcessorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class LocationProcessor implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerInterface $data, $object, Context $context)
    {
        if (!$object instanceof Location) {
            return;
        }

        $data->set('@type', 'GeoCoordinates');
        $data->set('latitude', $object->getLatitude());
        $data->set('longitude', $object->getLongitude());
    }
}
