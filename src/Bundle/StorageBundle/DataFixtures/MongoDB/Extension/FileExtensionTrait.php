<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\DataFixtures\MongoDB\Extension;

use Integrated\Bundle\ContentBundle\Document\Content\File;
use Integrated\Bundle\StorageBundle\DataFixtures\MongoDB\Util\CreateUtil;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
trait FileExtensionTrait
{
    /**
     * @return ContainerInterface
     */
    abstract public function getContainer();

    /**
     * @param string $path
     * @param string $name
     *
     * @return File
     */
    public function createFile($path, $name = '')
    {
        return (new File())
            ->setTitle($name)
            ->setFile(
                CreateUtil::path(
                    $this->getContainer()->get('integrated_storage.manager'),
                    $path
                )
            );
    }
}
