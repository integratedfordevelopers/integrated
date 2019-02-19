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

use Faker\Provider\Image;
use Integrated\Bundle\StorageBundle\DataFixtures\Faker\Util\CreateUtil;
use Integrated\Common\Content\Document\Storage\Embedded\StorageInterface;
use Integrated\Bundle\StorageBundle\Storage\Manager;

class ImageProvider
{
    /**
     * @var Manager
     */
    private $sm;

    /**
     * @param Manager $sm
     */
    public function __construct(Manager $sm)
    {
        $this->sm = $sm;
    }

    /**
     * @param int         $width
     * @param int         $height
     * @param string|null $category
     * @param string      $dir
     *
     * @return StorageInterface
     *
     * @throws \Exception
     */
    public function createImage($width = 640, $height = 480, $category = null, $dir = '/tmp')
    {
        return CreateUtil::path(
            $this->sm,
            Image::image($dir, $width, $height, $category)
        );
    }
}
