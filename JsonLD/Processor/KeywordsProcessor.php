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
use Integrated\Common\Content\Embedded\RelationInterface;
use Integrated\Common\Normalizer\ContainerInterface;
use Integrated\Common\Normalizer\Processor\Context;
use Integrated\Common\Normalizer\Processor\ProcessorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class KeywordsProcessor implements ProcessorInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerInterface $data, $object, Context $context)
    {
        if (!$object instanceof Content) {
            return;
        }

        // only add data on the root element.

        if ($context->getNesting()) {
            return;
        }

        $keywords = [];

        /** @var RelationInterface $relation */
        foreach ($object->getRelationsByRelationType('taxonomy') as $relation) {
            foreach ($relation->getReferences() as $content) {
                $keyword = '';

                if (method_exists($content, 'getTitle')) {
                    $keyword = trim($content->getTitle());
                } elseif (method_exists($content, 'getName')) {
                    $keyword = trim($content->getName());
                }

                if ($keyword) {
                    $keywords[$keyword] = $keyword;
                }
            }
        }

        if ($keywords) {
            $data->set('keywords', implode(', ', $keywords));
        }
    }
}
