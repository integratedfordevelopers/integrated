<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Mapping;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataFactory;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Integrated\Bundle\StorageBundle\Storage\Mapping\Property\EmbedOne;

/**
 * @author Jan Sanne Mulder <jansanne@e-active.nl>
 */
class MetadataFactory implements MetadataFactoryInterface
{
    /**
     * @var Metadata[]
     */
    private $cache = [];

    /**
     * @var ClassMetadataFactory
     */
    private $factory;

    /**
     * @var string
     */
    private $target;

    /**
     * @param ClassMetadataFactory $factory
     * @param string               $target
     */
    public function __construct(ClassMetadataFactory $factory, $target)
    {
        $this->factory = $factory;
        $this->target = $target;
    }

    /**
     * @param DocumentManager $manager
     * @param string          $target
     *
     * @return MetadataFactory
     */
    public static function create(DocumentManager $manager, $target)
    {
        return new self($manager->getMetadataFactory(), $target);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($class)
    {
        if (isset($this->cache[$class])) {
            return $this->cache[$class];
        }

        /** @var ClassMetadata $metadata */
        $metadata = $this->factory->getMetadataFor($class);

        // Class names are case insensitive so the class name could be all lower or upper case
        // and still be valid. So check again with the doctrine metadata class name as that
        // uses reflection to get the real class name. This should also take care of doctrine
        // proxy classes.

        if (isset($this->cache[$metadata->getName()])) {
            return $this->cache[$class] = $this->cache[$metadata->getName()];
        }

        $properties = [];

        foreach ($metadata->associationMappings as $mapping) {
            if (!empty($mapping['embedded']) && $mapping['type'] === 'one' && $mapping['targetDocument'] === $this->target) {
                $properties[] = new EmbedOne(isset($mapping['fieldName']) ? $mapping['fieldName'] : $mapping['name']);
            }
        }

        return $this->cache[$metadata->getName()] = new Metadata($metadata->getName(), $properties);
    }
}
