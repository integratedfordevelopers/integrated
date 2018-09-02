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

/**
 * @deprecated will be removed asap
 *
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class ClassMetadataLoadFinderSubscriber implements EventSubscriber
{
    /**
     * @var array
     */
    private $classes = [];

    /**
     * @var array
     */
    private $matches = [];

    public function hasMatch($class)
    {
        return isset($this->matches[strtolower($class)]);
    }

    public function addMatch($class)
    {
        $this->matches[strtolower($class)] = $class;
    }

    public function removeMatch($class)
    {
        $class = strtolower($class);

        if (isset($this->matches[$class])) {
            unset($this->matches[$class]);
        }
    }

    public function hasMatches()
    {
        return \count($this->matches) ? true : false;
    }

    public function setMatches(array $classes)
    {
        $this->clearMatches();

        foreach ($classes as $class) {
            $this->addMatch($class);
        }
    }

    public function getMatches()
    {
        return $this->matches;
    }

    public function clearMatches()
    {
        $this->matches = [];
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
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
        ];
    }

    /**
     * Check the class name against a list of classes to see if its loaded or not.
     *
     * @param LoadClassMetadataEventArgs $event
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $event)
    {
        $class = $event->getClassMetadata()->getName();

        if ($this->hasClass($class)) {
            $this->addMatch($class);
        }
    }
}
