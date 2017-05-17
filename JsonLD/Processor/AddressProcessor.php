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

use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Address;
use Integrated\Common\Normalizer\ContainerInterface;
use Integrated\Common\Normalizer\Processor\Context;
use Integrated\Common\Normalizer\Processor\ProcessorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class AddressProcessor implements ProcessorInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerInterface $data, $object, Context $context)
    {
        if (!$object instanceof Address) {
            return;
        }

        $data->set('@type', 'Place');
        $data->set('name', $object->getName());

        $data->set('address', [
            '@type' => 'PostalAddress',
            'postalCode' => $object->getZipcode(),
            'streetAddress' => $object->getAddress1(),
            'addressRegion' => $object->getState(),
            'addressCountry' => $object->getCountry()
        ]);

        if ($geo = $context->normalize($object->getLocation())) {
            $data->set('geo', $geo);
        }
    }
}
