<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\DataFixtures\Faker\Provider;

use Integrated\Bundle\StorageBundle\DataFixtures\MongoDB\Faker\Image;
use Integrated\Bundle\StorageBundle\DataFixtures\MongoDB\Util\CreateUtil;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Integrated\Bundle\StorageBundle\Storage\Manager;

class ImageProvider
{
    /**
     * @var Manager
     */
    private $sm;

    /**
     * ImageProvider constructor.
     * @param Manager $sm
     */
    public function __construct(Manager $sm)
    {
        $this->sm = $sm;
    }

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
            $this->sm,
            Image::image($dir, $width, $height, $category)
        );
    }
}
