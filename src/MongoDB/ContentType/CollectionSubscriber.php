<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\MongoDB\ContentType;

use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Event\LoadClassMetadataEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Integrated\MongoDB\ContentType\Exception\InvalidArgumentException;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class CollectionSubscriber implements EventSubscriber
{
    private $class;

    private $collection;

    /**
     * @param string $class
     * @param string $collection
     *
     * @throws InvalidArgumentException if the class does not exist
     */
    public function __construct($class, $collection)
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf('The class "%s" does not exist', $class));
        }

        $this->class = $class;
        $this->collection = $collection;
    }

    /**
     * return the class;.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * return the collection.
     *
     * @return string
     */
    public function getCollection()
    {
        return $this->collection;
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
     * Set the collection of the content class based on the configuration.
     *
     * @param LoadClassMetadataEventArgs $event
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $event)
    {
        $class = $event->getClassMetadata();

        if ($class->getName() == $this->class) {
            $class->setCollection($this->collection);
        }
    }
}
