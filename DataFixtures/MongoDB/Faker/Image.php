<?php

/*
 * This file is part of the Integrated package.
 *
 * (c) e-Active B.V. <integrated@e-active.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Integrated\Bundle\StorageBundle\DataFixtures\MongoDB\Faker;

/**
 * @author Johan Liefers <johan@e-active.nl>
 */
class Image extends \Faker\Provider\Image
{
    /**
     * We don't need the faker generator from the base class
     */
    public function __construct()
    {
    }

    /**
     * @param int $width
     * @param int $height
     * @param null $category
     * @param bool $randomize
     * @param null $word
     * @return string
     */
    public static function imageUrl($width = 640, $height = 480, $category = null, $randomize = true, $word = null)
    {
        $url = "http://wospixel.e-activesites.nl/{$width}/{$height}/";

        if ($category) {
            $url .= "{$category}/";
        }
        
        return $url;
    }
}
