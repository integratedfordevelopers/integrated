<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\Storage\Cache;

use Integrated\Bundle\StorageBundle\Exception\NoFilesystemAvailableException;
use Integrated\Bundle\StorageBundle\Storage\Util\DirectoryUtil;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Integrated\Common\Storage\Cache\CacheInterface;
use Integrated\Common\Storage\ManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
class AppCache implements CacheInterface
{
    /**
     * @const
     */
    const CACHE_PATH = '%s/integrated/storage/file';

    /**
     * @var ManagerInterface
     */
    private $fileManager;

    /**
     * @var string
     */
    private $directory;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param string           $directory
     * @param ManagerInterface $managerInterface
     * @param RequestStack     $requestStack
     */
    public function __construct(string $directory, ManagerInterface $managerInterface, RequestStack $requestStack)
    {
        $this->fileManager = $managerInterface;
        $this->directory = $directory;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LogicException
     */
    public function path(StorageInterface $storage)
    {
        if ($file = $this->getLocalFile($storage)) {
            return $file;
        }

        // Attempt to make a local copy of the file, it's probably not in a public storage
        $file = DirectoryUtil::cachePathFile(sprintf(self::CACHE_PATH, $this->directory), $storage);

        // Check if a file exists
        if ($file->isFile()) {
            return $file;
        }

        // Whenever the filesystem(s) are down or does not contain the file (anymore) we'll end up with this exception
        try {
            // Read it
            $content = $this->fileManager->read($storage);
        } catch (NoFilesystemAvailableException $exception) {
            throw new \InvalidArgumentException($exception->getMessage());
        }

        // Do not put an empty file in cache, otherwise GD will throw a fatal
        if (!$content) {
            throw new \InvalidArgumentException('File is empty');
        }

        // Open a file with write permission
        $write = $file->openFile('w');
        if ($write->isWritable()) {
            // Write it if we've got some content
            $write->fwrite($content);

            return $write->openFile('r');
        }

        // Let's give it to the requestee, we failed
        throw new \LogicException(
            'The directory %s is not writable or the cache directory does not exist.',
            $this->directory
        );
    }

    /**
     * @param StorageInterface $storage
     *
     * @return bool|\SplFileObject
     */
    private function getLocalFile(StorageInterface $storage)
    {
        if (!$storage->getPathname()) {
            return false;
        }

        if ($request = $this->requestStack->getMasterRequest()) {
            $file = $request->server->get('DOCUMENT_ROOT').$request->getBasePath().$storage->getPathname();
            if (file_exists($file)) {
                return new \SplFileObject($file, 'r');
            }
        }

        return false;
    }
}
