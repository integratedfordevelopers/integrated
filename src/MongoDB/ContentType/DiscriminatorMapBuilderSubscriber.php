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
use Doctrine\ODM\MongoDB\Event\LoadClassMetadataEventArgs;
use Doctrine\ODM\MongoDB\Events;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataFactory;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataInfo;

/**
 * @deprecated will be removed asap
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class DiscriminatorMapBuilderSubscriber implements EventSubscriber
{
    /**
     * @var ClassMetadataFactory
     */
    private $factory;

    /**
     * @var string[]
     */
    private $classes;

    /**
     * @var ClassMetadataInfo[]
     */
    private $parents = [];

    /**
     * @var ClassMetadataInfo[][]
     */
    private $children = [];

    /**
     * @var ClassMetadataInfo[]
     */
    private $changes = [];

    public function __construct(ClassMetadataFactory $factory)
    {
        $this->factory = $factory;
    }

    public function hasClass($class)
    {
        return isset($this->classes[strtolower($class)]);
    }

    public function addClass($class)
    {
        $this->classes[strtolower($class)] = $class;
    }

    public function removeClass($class)
    {
        $class = strtolower($class);

        if (isset($this->classes[$class])) {
            unset($this->classes[$class]);
        }
    }

    public function hasClasses()
    {
        return \count($this->classes) ? true : false;
    }

    public function setClasses(array $classes)
    {
        $this->clearClasses();

        foreach ($classes as $class) {
            $this->addClass($class);
        }
    }

    public function getClasses()
    {
        return $this->classes;
    }

    public function clearClasses()
    {
        $this->classes = [];
    }

    /**
     * @return bool
     */
    public function hasChanges()
    {
        return \count($this->changes) ? true : false;
    }

    /**
     * @return ClassMetadataInfo[]
     */
    public function getChanges()
    {
        return $this->changes;
    }

    public function clearChanges()
    {
        $this->changes = [];
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
     * @param LoadClassMetadataEventArgs $event
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $event)
    {
        $class = $event->getClassMetadata();

        if (!$this->hasClass($class->rootDocumentName)) {
            return;
        }

        if ($class->getName() == $class->rootDocumentName) {
            $this->setParent($class);
        } else {
            $this->addChild($class);
        }

        $this->changes[$class->name] = $class;
    }

    /**
     * This is only called when the parent was not loaded yet
     * so it means no children are found yet as doctrine first
     * load the parent classes and then the actual class.
     *
     * This will prepare the ClassMetadataInfo by resetting some
     * options.
     *
     * @param ClassMetadataInfo $class
     */
    private function setParent(ClassMetadataInfo $class)
    {
        $this->parents[$class->name] = $class;
        $this->children[$class->name] = [];

        // Reset discriminator and subclasses config as this will be build
        // automatically.
        // NOTE: doctrine doc comments claim these properties are read only

        $class->discriminatorMap = [];
        $class->discriminatorValue = null;

        $class->subClasses = [];
    }

    /**
     * Get the parent class.
     *
     * When not parent class is set load it from the factory and then
     * also load all its subclasses.
     *
     * @param ClassMetadataInfo $class
     *
     * @return ClassMetadataInfo
     */
    private function getParent(ClassMetadataInfo $class)
    {
        if (isset($this->parents[$class->rootDocumentName])) {
            return $this->parents[$class->rootDocumentName];
        }

        // The metadata should already be cashed but in case its not we first
        // set the parent so that no infinite is possible.

        /* @var ClassMetadataInfo $parent */

        $this->parents[$class->rootDocumentName] = $parent = $this->factory->getMetadataFor($class->rootDocumentName);
        $this->children[$class->rootDocumentName] = [];

        foreach ($parent->subClasses as $class) {
            $this->addChild($this->factory->getMetadataFor($class));
        }

        // get all the parent classes from the children as its possible that
        // there are mapped super classes which are not in the sub classes list.

        /** @var ClassMetadataInfo $child */
        $parents = [];

        foreach ($this->children[$parent->name] as $child) {
            foreach ($child->parentClasses as $class) {
                $parents[$class] = $class;
            }
        }

        // root will not be in the discriminator map as that will be the default
        // loaded class anyways.

        unset($parents[$parent->name]);

        foreach ($parents as $class) {
            if (!isset($this->children[$parent->name][$class])) {
                $this->addChild($this->factory->getMetadataFor($class));
            }
        }

        return $parent;
    }

    /**
     * @param ClassMetadataInfo $class
     */
    private function addChild(ClassMetadataInfo $class)
    {
        $parent = $this->getParent($class);

        // check if the child is already added to the children array

        if (isset($this->children[$parent->name][$class->name])) {
            return;
        }

        $this->children[$parent->name][$class->name] = $class;

        if ($class->isMappedSuperclass) {
            $class->setDiscriminatorMap($parent->discriminatorMap);

            return; // there is no update to discriminator map
        }

        // Every time a child is added the map need to be distribute to all
        // the children

        $parent->setDiscriminatorMap([$class->getName() => $class->getName()]); // it's called set but is implemented as a add.

        /** @var ClassMetadataInfo $child */
        foreach ($this->children[$parent->name] as $child) {
            $child->setDiscriminatorMap($parent->discriminatorMap);
        }

        $this->changes = $this->changes + $this->children[$parent->name];
        $this->changes[$parent->name] = $parent;
    }
}
