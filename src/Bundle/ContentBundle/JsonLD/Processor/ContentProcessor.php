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
use Integrated\Bundle\ContentBundle\JsonLD\UrlGenerator;
use Integrated\Common\Normalizer\ContainerInterface;
use Integrated\Common\Normalizer\Processor\Context;
use Integrated\Common\Normalizer\Processor\ProcessorInterface;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ContentProcessor implements ProcessorInterface
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
     * {@inheritdoc}
     */
    public function process(ContainerInterface $data, $object, Context $context)
    {
        if (!$object instanceof Content) {
            return;
        }

        $data->set('url', $this->generator->generateUrl($object));

        // only add data on the root element.

        if ($context->getNesting()) {
            return;
        }

        $data->set('dateCreated', $object->getCreatedAt()->format('c'));
        $data->set('dateModified', $object->getUpdatedAt()->format('c'));

        if ($date = $object->getPublishTime()->getStartDate()) {
            $data->set('datePublished', $date->format('c'));
        }

        $data->set('mainEntityOfPage', [
            '@type' => 'WebPage',
            '@id' => $this->generator->generateUrl($object),
        ]);
    }
}
