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

use Integrated\Bundle\StorageBundle\Storage\Reflection\ReflectionCacheInterface;
use Integrated\Bundle\StorageBundle\Storage\Reflection\PropertyReflection;
use Integrated\Bundle\StorageBundle\Storage\Util\DirectoryUtil;

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
     * @param string $environment
     * @param string $directory
     */
    public function __construct($environment, $directory)
    {
        $this->environment = $environment;
        $this->directory = $directory;
    }

    /**
     * @param string $class
     * @return PropertyReflection
     */
    public function getPropertyReflectionClass($class)
    {
        $file = new \SplFileInfo(
            sprintf(
                self::CACHE_PATH,
                $this->directory,
                sha1(sprintf('%s_%s', __FILE__, $class))
            )
        );

        if ($file->isFile()) {
            return unserialize(
                $file->openFile()->fread($file->getSize())
            );
        }

        $reflection = new PropertyReflection($class);
        $reflection->getTargetProperties();

        DirectoryUtil::createDirectory($this->directory, $file->getPath());
        $file->openFile('w')
            ->fwrite(serialize($reflection));

        return $reflection;
    }

}
