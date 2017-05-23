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

use Integrated\Bundle\ContentBundle\Document\Content\Relation\Person;
use Integrated\Common\Normalizer\ContainerInterface;
use Integrated\Common\Normalizer\Processor\Context;
use Integrated\Common\Normalizer\Processor\ProcessorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class PersonProcessor implements ProcessorInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerInterface $data, $object, Context $context)
    {
        if (!$object instanceof Person) {
            return;
        }

        $data->set('@type', 'Person');
        $data->set('name', implode(' ', array_filter([$object->getFirstName(), $object->getLastName()])));
        $data->set('givenName', $object->getFirstName());
        $data->set('familyName', $object->getLastName());
        $data->set('additionalName', $object->getPrefix());

        foreach ($object->getPhonenumbers() as $number) {
            $data->add('contactPoint', [
                '@type' => 'ContactPoint',
                'contactType' => $number->getType(),
                'telephone' => $number->getNumber()
            ]);
        }

        foreach ($object->getJobs() as $job) {
            if ($location = $context->normalize($job->getCompany())) {
                $data->add('worksFor', $location);
            }
        }

        foreach ($object->getAddresses() as $address) {
            if ($location = $context->normalize($address)) {
                $data->add('workLocation', $location);
            }
        }
    }
}
