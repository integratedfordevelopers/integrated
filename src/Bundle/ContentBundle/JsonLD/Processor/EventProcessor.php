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

use Integrated\Bundle\ContentBundle\Document\Content\Event;
use Integrated\Common\Normalizer\ContainerInterface;
use Integrated\Common\Normalizer\Processor\Context;
use Integrated\Common\Normalizer\Processor\ProcessorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class EventProcessor implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerInterface $data, $object, Context $context)
    {
        if (!$object instanceof Event) {
            return;
        }

        $data->set('@type', 'Event');

        if ($date = $object->getStartDate()) {
            $data->set('startDate', $date->format('c'));
        }

        if ($date = $object->getEndDate()) {
            $data->set('endDate', $date->format('c'));
        }
    }
}
