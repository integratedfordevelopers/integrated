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

use Integrated\Bundle\StorageBundle\DataFixtures\MongoDB\Faker\Image;
use Integrated\Bundle\StorageBundle\DataFixtures\MongoDB\Util\CreateUtil;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Johnny Borg <johnny@e-active.nl>
 */
trait ImageExtensionTrait
{
    /**
     * @return ContainerInterface
     */
    abstract public function getContainer();

    /**
     * @param int    $width
     * @param int    $height
     * @param null   $category
     * @param string $dir
     *
     * @return StorageInterface
     */
    public function createImage($width = 640, $height = 480, $category = null, $dir = '/tmp')
    {
        return CreateUtil::path(
            $this->getContainer()->get('integrated_storage.manager'),
            Image::image($dir, $width, $height, $category)
        );
    }
}
