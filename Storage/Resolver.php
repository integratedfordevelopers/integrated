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
use Integrated\Bundle\StorageBundle\Document\Embedded\Storage;
use Integrated\Bundle\StorageBundle\Storage\Identifier\IdentifierInterface;
use Integrated\Bundle\StorageBundle\Storage\Reader\ReaderInterface;
use Integrated\Bundle\StorageBundle\Storage\Registry\FilesystemRegistry;
use Integrated\Bundle\StorageBundle\Storage\Resolver\ResolverInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class Resolver
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
     * @param array $resolverMap
     * @param IdentifierInterface $identifier
     * @param FilesystemRegistry $registry
     */
    public function __construct(array $resolverMap, IdentifierInterface $identifier, FilesystemRegistry $registry)
    {
        $this->resolverMap = $resolverMap;
        $this->identifier = $identifier;
        $this->registry = $registry;
    }

    /**
     * Gives you an absolute path to the storage.
     * A preference can be given. When the preference is not able to serve the file another filesystem will be used.
     *
     * @param Storage $storage
     * @param ArrayCollection $filesystem
     * @return string absolute path
     */
    public function resolve(Storage $storage, ArrayCollection $filesystem = null)
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
     * @param ReaderInterface $reader
     * @return string
     */
    public function getIdentifier(ReaderInterface $reader)
    {
        return $this->identifier->getIdentifier($reader);
    }

    /**
     * @param $filesystem
     * @return array|bool
     */
    public function getOptions($filesystem)
    {
        if (isset($this->resolverMap[$filesystem])) {
            return $this->resolverMap[$filesystem];
        }

        return false;
    }

    /**
     * Create a resolver class based on the options
     *
     * @param string $filesystem
     * @param string $identifier
     * @return ResolverInterface
     */
    public function getResolverClass($filesystem, $identifier)
    {
        $className = $this->resolverMap[$filesystem]['resolver_class'];

        $resolver = new $className($this->resolverMap[$filesystem], $identifier);
        if ($resolver instanceof ResolverInterface) {
            return $resolver;
        }

        throw new \LogicException(
            sprintf(
                'Class %s must implement Integrated\Bundle\StorageBundle\Storage\Resolver\ResolverInterface',
                get_class($resolver)
            )
        );
    }
}
