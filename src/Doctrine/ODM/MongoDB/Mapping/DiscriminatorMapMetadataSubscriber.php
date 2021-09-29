<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Doctrine\ODM\MongoDB\Mapping;

use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LoadClassMetadataEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class DiscriminatorMapMetadataSubscriber implements EventSubscriber
{
    /**
     * @var DiscriminatorMapResolverInterface
     */
    private $resolver;

    /**
     * Constructor.
     *
     * @param DiscriminatorMapResolverInterface $resolver
     */
    public function __construct(DiscriminatorMapResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
        ];
    }

    /**
     * Resolve and replace the discriminator map.
     *
     * This will try to resolve the discriminator map and if found it will replace the current
     * discriminator map and subclasses config in the given metadata.
     *
     * @param LoadClassMetadataEventArgs $event
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $event)
    {
        $metadata = $event->getClassMetadata();

        if (!$metadata instanceof ClassMetadata) {
            return;
        }

        if ($map = $this->resolver->resolve($metadata->getName())) {
            // Reset discriminator and subclasses config as the setters for those values are
            // actually add methods.
            // NOTE: doctrine doc comments claim these properties are read only

            $metadata->discriminatorMap = [];
            $metadata->discriminatorValue = null;

            $metadata->subClasses = [];

            $metadata->setDiscriminatorMap($map); // also set subclasses
        }
    }
}
