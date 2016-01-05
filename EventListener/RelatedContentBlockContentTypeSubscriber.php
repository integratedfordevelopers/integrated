<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\ContentBundle\EventListener;

use Doctrine\Common\Persistence\ObjectRepository;
use Integrated\Common\Form\Mapping\Event\MetadataEvent;
use Integrated\Common\Form\Mapping\Events;
use Integrated\Common\Form\Mapping\MetadataFactory;
use Integrated\Common\Form\Mapping\Metadata\Field;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Integrated\Bundle\ContentBundle\Document\ContentType\ContentType;

/**
 * @author Vasil Pascal <developer.optimum@gmail.com>
 */
class RelatedContentBlockContentTypeSubscriber implements EventSubscriberInterface
{

    /**
     * @var MetadataFactory
     */
    private $factory;

    /**
     * @var ObjectRepository
     */
    private $repository;

    /**
     * RelatedContentBlockContentTypeSubscriber constructor.
     * @param MetadataFactory  $factory
     * @param ObjectRepository $repository
     */
    public function __construct(MetadataFactory $factory, ObjectRepository $repository)
    {
        $this->factory = $factory;
        $this->repository = $repository;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onRequest',
            Events::METADATA => 'onMetaData',
        ];
    }

    /**
     * @inheritdoc
     */
    public function onRequest()
    {
        $this->factory->getEventDispatcher()->addSubscriber($this);
    }

    /**
     * @param MetadataEvent $event
     */
    public function onMetaData(MetadataEvent $event)
    {
        $metadata = $event->getMetadata();

        if ($metadata->getClass() !== 'Integrated\Bundle\ContentBundle\Document\Block\RelatedContentBlock') {
            return;
        }

        if ($metadata->hasField('contentType')) {
            /** @var Field $field */
            $field = $metadata->getField('contentType');

            $contentTypes = $this->repository->findAll();
            $choices = [];

            /** @var ContentType $contentType */
            foreach ($contentTypes as $contentType) {
                $choices[$contentType->getId()] = $contentType->getName();
            }
            $field->setOption('choices', $choices);
        }
    }
}
