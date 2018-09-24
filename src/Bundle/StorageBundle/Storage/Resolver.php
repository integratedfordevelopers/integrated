<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage;

use Doctrine\Common\Collections\ArrayCollection;
use Integrated\Bundle\StorageBundle\Storage\Registry\FilesystemRegistry;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Integrated\Common\Storage\FileResolver\FileResolverInterface;
use Integrated\Common\Storage\FilesystemRegistryInterface;
use Integrated\Common\Storage\Identifier\IdentifierInterface;
use Integrated\Common\Storage\Reader\ReaderInterface;
use Integrated\Common\Storage\ResolverInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class Resolver implements ResolverInterface
{
    /**
     * @var array
     */
    protected $resolverMap = [];

    /**
     * @var IdentifierInterface
     */
    protected $identifier;

    /**
     * @var FilesystemRegistry
     */
    protected $registry;

    /**
     * @param array                       $resolverMap
     * @param IdentifierInterface         $identifier
     * @param FilesystemRegistryInterface $registry
     */
    public function __construct(array $resolverMap, IdentifierInterface $identifier, FilesystemRegistryInterface $registry)
    {
        $this->resolverMap = $resolverMap;
        $this->identifier = $identifier;
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(StorageInterface $storage, ArrayCollection $filesystem = null)
    {
        $priority = $filesystem ? $filesystem : $storage->getFilesystems();
        $priority->getIterator()->uasort(function ($a) use ($filesystem) {
            // The given filesystem always has priority, however it might not be able to serve the file
            return $a == $filesystem ? -1 : 1;
        });

        // Attempt to find a URL by the defined priority (or something like that)
        foreach ($priority as $key) {
            if (isset($this->resolverMap[$key])) {
                if ($this->registry->get($key)->has($storage->getIdentifier())) {
                    return $this->getResolverClass($key, $storage->getIdentifier())->getLocation();
                }
            }
        }

        // Show never happen, a resolver can not fail and at least one resolver must be defined in the configuration
        throw new \LogicException(
            sprintf(
                'No valid public path found for %s in filesystems: %s',
                $storage->getIdentifier(),
                implode(', ', $storage->getFilesystems())
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier(ReaderInterface $reader)
    {
        return $this->identifier->getIdentifier($reader);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions($filesystem)
    {
        if (isset($this->resolverMap[$filesystem])) {
            return $this->resolverMap[$filesystem];
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getResolverClass($filesystem, $identifier)
    {
        $className = $this->resolverMap[$filesystem]['resolver_class'];

        $resolver = new $className($this->resolverMap[$filesystem], $identifier);
        if ($resolver instanceof FileResolverInterface) {
            return $resolver;
        }

        throw new \LogicException(
            sprintf(
                'Class %s must implement Integrated\Bundle\StorageBundle\Storage\Resolver\ResolverInterface',
                \get_class($resolver)
            )
        );
    }
}
