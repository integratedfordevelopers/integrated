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

use Integrated\Bundle\ContentBundle\Document\Content\Relation\Company;
use Integrated\Bundle\ContentBundle\JsonLD\UrlGenerator;
use Integrated\Common\Normalizer\ContainerInterface;
use Integrated\Common\Normalizer\Processor\Context;
use Integrated\Common\Normalizer\Processor\ProcessorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class CompanyProcessor implements ProcessorInterface
{
    /**
     * @var UrlGenerator
     */
    protected $generator;

    /**
     * @param UrlGenerator $generator
     */
    public function __construct(UrlGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * {@inheritDoc}
     */
    public function process(ContainerInterface $data, $object, Context $context)
    {
        if (!$object instanceof Company) {
            return;
        }

        $data->set('@type', 'Organization');
        $data->set('name', $object->getName());

        if ($numbers = $object->getPhonenumbers()) {
            if (isset($numbers[0])) {
                $data->set('telephone', $numbers[0]->getNumber());
            }

            foreach ($numbers as $number) {
                $data->add('contactPoint', [
                    '@type' => 'ContactPoint',
                    'contactType' => $number->getType(),
                    'telephone' => $number->getNumber()
                ]);
            }
        }

        foreach ($object->getAddresses() as $address) {
            if ($location = $context->normalize($address)) {
                $data->add('location', $location);
            }
        }

        if ($logo = $object->getLogo()) {
            $data->set('logo', $this->generator->generateUrl($object->getLogo()));
        }
    }
}
