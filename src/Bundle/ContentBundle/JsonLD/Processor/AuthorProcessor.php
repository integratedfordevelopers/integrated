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

use Integrated\Bundle\ContentBundle\Document\Content\Embedded\Author;
use Integrated\Common\Normalizer\ContainerInterface;
use Integrated\Common\Normalizer\Processor\Context;
use Integrated\Common\Normalizer\Processor\ProcessorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class AuthorProcessor implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerInterface $data, $object, Context $context)
    {
        if (!$object instanceof Author) {
            return;
        }

        $data->set('@type', 'Person');
        $data->set('gender', $object->getPerson()->getGender());
        $data->set('givenName', $object->getPerson()->getFirstName());
        $data->set('familyName', $object->getPerson()->getLastName());
        $data->set('additionalName', $object->getPerson()->getPrefix());
    }
}
