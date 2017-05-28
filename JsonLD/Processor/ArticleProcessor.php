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

use Integrated\Bundle\ContentBundle\Document\Content\Article;
use Integrated\Common\Normalizer\ContainerInterface;
use Integrated\Common\Normalizer\Processor\Context;
use Integrated\Common\Normalizer\Processor\ProcessorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ArticleProcessor implements ProcessorInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerInterface $data, $object, Context $context)
    {
        if (!$object instanceof Article) {
            return;
        }

        $data->set('@type', 'Article');
        $data->set('name', $object->getTitle());
        $data->set('headline', $object->getTitle());
        $data->set('description', $object->getDescription());

        foreach ($object->getAuthors() as $author) {
            if ($author = $context->normalize($author)) {
                $data->add('author', $author);
            }
        }

        if ($location = $context->normalize($object->getAddress())) {
            $data->set('contentLocation', $location);
        }
    }
}
