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

use Integrated\Bundle\ContentBundle\Document\Content\Content;
use Integrated\Bundle\ContentBundle\Document\Content\Image;
use Integrated\Common\Content\Embedded\RelationInterface;
use Integrated\Common\Normalizer\ContainerInterface;
use Integrated\Common\Normalizer\Processor\Context;
use Integrated\Common\Normalizer\Processor\ProcessorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ImagesProcessor implements ProcessorInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerInterface $data, $object, Context $context)
    {
        if (!$object instanceof Content) {
            return;
        }

        /** @var RelationInterface $relation */
        foreach ($object->getRelationsByRelationType('embedded') as $relation) {
            foreach ($relation->getReferences() as $content) {
                if ($content instanceof Image && $content = $context->normalize($content)) {
                    $data->add('image', $content);
                }
            }
        }
    }
}
