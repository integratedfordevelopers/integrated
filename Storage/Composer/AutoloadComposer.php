<?php

namespace Integrated\Bundle\StorageBundle\Storage\Composer;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
final class AutoloadComposer
{
    /**
     * @const string
     */
    const VENDOR_PATH = 'vendor/composer';

    /**
     * @param string $rootPath
     * @return array
     * @throws \InvalidArgumentException
     */
    public static function getNamespaces($rootPath)
    {
        $file = sprintf('%s/%s/%s', $rootPath, self::VENDOR_PATH, 'autoload_namespaces.php');
        if (file_exists($file)) {
            return require $file;
        } else {
            throw new \InvalidArgumentException(
                sprintf(
                    'The path "%s" did not contain "%s" or does not have the "autoload_namespaces.php" file.',
                    $rootPath,
                    self::VENDOR_PATH
                )
            );
        }
    }
}
