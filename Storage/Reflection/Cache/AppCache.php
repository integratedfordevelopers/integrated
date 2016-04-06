<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Reflection\Cache;

use Doctrine\Common\Collections\ArrayCollection;

use Integrated\Bundle\StorageBundle\Storage\Reflection\ReflectionCacheInterface;
use Integrated\Bundle\StorageBundle\Storage\Reflection\PropertyReflection;
use Integrated\Bundle\StorageBundle\Storage\Util\DirectoryUtil;
use Symfony\Component\Form\Exception\LogicException;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class AppCache implements ReflectionCacheInterface
{
    /**
     * @const
     */
    const CACHE_PATH = '%s/integrated/storage/reflection/%s';

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var ArrayCollection
     */
    protected $cache;

    /**
     * @param string $environment
     * @param string $directory
     */
    public function __construct($environment, $directory)
    {
        $this->environment = $environment;
        $this->directory = $directory;
        $this->cache = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getPropertyReflectionClass($class)
    {
        // The object
        $reflection = null;

        // Check the local cache for an early out
        if ($this->cache->contains($class)) {
            $reflection = $this->cache->get($class);
        }

        // Check the disk cache
        if (null == $reflection) {
            // Build a file object read
            $file = new \SplFileInfo(
                sprintf(
                    self::CACHE_PATH,
                    $this->directory,
                    sha1(sprintf('%s_%s', __FILE__, $class))
                )
            );

            if ($file->isFile()) {
                // Read operation
                $reflection = unserialize($file->openFile()->fread($file->getSize()));

                // Sanity
                if (false == ($reflection instanceof PropertyReflection)) {
                    // Invalid result
                    throw new LogicException(
                        'Unexpected result from reflection cache %s given but %s expected',
                        is_object($reflection) ? get_class($reflection) : gettype($reflection),
                        PropertyReflection::class
                    );
                }
            }
        }

        // Check
        if (null == $reflection || 'dev' == $this->environment) {
            // Build new property reflection and do a one time lookup
            $reflection = new PropertyReflection($class);
            $reflection->getTargetProperties();

            // Write the reflection
            DirectoryUtil::createDirectory($this->directory, $file->getPath());
            $file->openFile('w')->fwrite(serialize($reflection));
        }

        // Add to the cache to prevent a disk lookup
        $this->cache->set($class, $reflection);

        return $reflection;
    }

}
